// SPDX-License-Identifier: MIT
pragma solidity ^0.8.7;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Counters.sol";
import "@chainlink/contracts/src/v0.8/interfaces/VRFCoordinatorV2Interface.sol";
import "@chainlink/contracts/src/v0.8/VRFConsumerBaseV2.sol";
//import "JedstarNFT.sol";

interface IBEP20 {
    function decimals() external view returns (uint8);

    function totalSupply() external view returns (uint256);

    function balanceOf(address account) external view returns (uint256);

    function transfer(address recipient, uint256 amount)
        external
        returns (bool);

    function allowance(address owner, address spender)
        external
        view
        returns (uint256);

    function approve(address spender, uint256 amount) external returns (bool);

    function transferFrom(
        address sender,
        address recipient,
        uint256 amount
    ) external returns (bool);
}

interface JedstarNFT {
  /**
   * @dev Support discovery of user NFTs by wallet address
   */
  function getTokenIdsByWallet(address _walletAddress) external view returns (uint256[] memory);

  /**
   * @dev There are multiple different items that are available, this method
   * allows for the specific item to be identified from the token ID
   */
  function getItemIndex(uint256 _tokenId) external view returns (uint256);

  /**
   * @dev There are different tiers of items available, this method allows for
   * the item's tier to be identified from the token ID
   */
  function getItemTier(uint256 _tokenId) external view returns (uint8);

  /**
   * @dev Each item has its own properties, this method allows for those
   * properties to be retrieved
   */
  function getItemProperties(uint256 _tokenId) external view returns (uint256[] memory);
   /**
   * @dev Each item supports additional data being associated with it, this
   * method allows that data to be retrieved
   */
  function getItemExtra(uint256 _tokenId) external view returns (string memory);

  /**
   * @dev Enables the extra data field to be updated for a specific NFT
   */
  function setItemExtra(uint256 _tokenId, string memory _extraData) external;

  /**
   * @dev Allows the randomised properties of the NFT to be regenerated
   */
  function regenProperties(uint256 _tokenId, address _coin) external;

  /**
   * @dev Identify when the item properties were last regenerated
   */
  function getItemLastGenTime(uint256 _tokenID) external view returns (uint256);

  /**
   * @dev Enables regen cost to be queried
   */
  function getRegenCostByCoin(address _coin) external view returns (uint256);
}


contract JedNFTs is IERC721, ERC721URIStorage, Ownable, VRFConsumerBaseV2, JedstarNFT {
  //Index of next token to be minted
  uint256 private _counter;
  modifier tokenExists(uint256 _tokenId) {
    require(_tokenId < _counter, "Token ID not recognised");
    _;
  }

  //isTrusted modifier
  address[] private trustedWallets;
  modifier isTrusted() {
    require(trustedWallets.length > 0, "Caller is not trusted");
    bool found;
    for (uint8 i = 0; i < trustedWallets.length && !found; i++){
      if (trustedWallets[i] == msg.sender){
        found = true;
      }
    }
    require(found, "Caller is not trusted");
    _;
  }

  //is the owner or approved handler
  modifier ownerOrApproved(address _handler, uint256 _tokenId) {
    require(_handler != address(0), "Zero address is not an approved handler");
    require(_tokenId < _counter, "Token ID not recognised");
    require(
      _owners[_tokenId] == _handler || //User is the owner
      getApproved(_tokenId) == _handler || //User is explicitly approved for this token
      isApprovedForAll(_owners[_tokenId], _handler), //User is approved to handle all assets belonging to this account (of which this token is one)
      "Not an approved handler for this token"
    );
    _;
  }

  address public jedCoin = 0x058a7Af19BdB63411d0a84e79E3312610D7fa90c;
  //Minimum amount of jedCoin required to take part in presale
  uint256 public jedCoinPresaleMin = 10000;
  //Set KRED token address
  address public kredCoin = 0xeA79d3a3a123C311939141106b0A9B1a5623696f;
  //Set cost to regen character in KRED
  uint256 private kredCoinRegenCost = 195000;
  //NFT properties
  struct Item {
    uint256 productIndex;
    uint256 propertyInt;
    uint256 lastGenTime;
    uint8 productTier;
    string extraData;
  }
  //The mapping of token ID to defined characters
  mapping(uint256 => Item) public tokenIdToItem;
  //The JED volume regen requirement
  uint256[4] jedRegenReq = [10000e18, 15000e18, 20000e18, 25000e18];

  //The maximum number of properties available for characters
  struct ItemProperties{
    uint16 propertyCount;
    uint16[5] tierFreeAssigns;
  }
  ItemProperties[] private itemProps;

  //The maximum number of loops to run through to populate properties
  uint16 private propertyIterationMax = 32;
  //Max per account during presale
  uint16 public presaleBuyLimit = 5;
  //Sale status
  bool public preSaleOn = false;
  bool public publicSaleOn = false;

  //Transfer permissions
  bool public transfersAllowed = true;

  //mapping of token ID to wallet owner
  mapping(uint256 => address) private _owners;

  // Mapping owner address to token count
  mapping(address => uint256) private _balances;

  //Mapping owner address to token IDs
  mapping(address => uint256[]) private _tokensByOwner;

  struct Products{
    string productName;
    uint16 productType;
    uint256[5] mintLimits;
    uint256[5] totalMints;
    uint256[5] baseMintCostPerTier; //assume price in cents
  }

  Products[] private allProds;

  //The multiplier to apply to the baseline cost based on the coin
  mapping(address => uint256) coinCostMultiplier;
  //The coin cost on supported coins for regen
  mapping(address => uint256) supportedCoinRegenCost;

  //Per address mint volume map
  mapping (address => uint256) public totalPerAccount;

  //Chainlink VRF props
  VRFCoordinatorV2Interface VRFCoordinator;
  bytes32 internal keyHash;
  uint256 internal fee;
  uint64 internal chainlinkVRFsubscriptionId;
  uint32 internal callbackGasLimit = 500000;
  uint32 internal numWords = 1;
  uint16 internal requestConfirmations = 3;
  mapping (uint256 => uint256) private vrfIdToTokenId;
  mapping (uint256 => uint256[]) private tokenIdToVRFs;

  constructor(string memory _name, string memory _ticker, address _vrfCoordinator, uint64 _vrfSubscriptionId) ERC721(_name, _ticker) VRFConsumerBaseV2(_vrfCoordinator) {
    VRFCoordinator = VRFCoordinatorV2Interface(_vrfCoordinator);
    keyHash = 0xd4bb89654db74673a187bd804519e65e3f71a52bc55f11da7601a13dcf505314;
    chainlinkVRFsubscriptionId = _vrfSubscriptionId;
    fee = 10 ** 18;

    Products memory charA;
    charA.productName = "";
    charA.productType = 1;
    charA.mintLimits = [uint256(20),50,500,5000,10000];
    charA.totalMints = [uint256(0),0,0,0,0];
    charA.baseMintCostPerTier = [uint256(1),1,1,1,1];
    allProds.push(charA);

    Products memory charB;
    charB.productName = "";
    charB.productType = 1;
    charB.mintLimits = [uint256(20),50,500,5000,10000];
    charB.totalMints = [uint256(0),0,0,0,0];
    charB.baseMintCostPerTier = [uint256(1),1,1,1,1];
    allProds.push(charB);

    Products memory charC;
    charC.productName = "";
    charC.productType = 1;
    charC.mintLimits = [uint256(20),50,500,5000,10000];
    charC.totalMints = [uint256(0),0,0,0,0];
    charC.baseMintCostPerTier = [uint256(1),1,1,1,1];
    allProds.push(charC);

    coinCostMultiplier[0x55d398326f99059fF775485246999027B3197955] = 1e16; //USDT
    coinCostMultiplier[0x1AF3F329e8BE154074D8769D1FFa4eE058B1DBc3] = 1e16; //DAI
    coinCostMultiplier[0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56] = 1e16; //BUSD
    coinCostMultiplier[0x8AC76a51cc950d9822D68b83fE1Ad97B32Cd580d] = 1e16; //USDC
    coinCostMultiplier[0x071549f11ade1044d338A66ABA6fA1903684Bec9] = 192600e16; //KREDT 0xeA79d3a3a123C311939141106b0A9B1a5623696f, [10,9,8,7,6] //KRED
    _counter = 1;
    //Define KRED as the currency required for regen
    supportedCoinRegenCost[0xeA79d3a3a123C311939141106b0A9B1a5623696f] = uint256(192600); //KRED

    //The first base set of characteristics for each item
    ItemProperties memory initProps;
    initProps.propertyCount = 5;
    initProps.tierFreeAssigns = [uint16(5),4,3,2,1];
    itemProps.push(initProps);
  }

  function mint(uint256 _productId, uint8 _tierId, address _coin, address _to) public returns (uint256) {
    require(preSaleOn, "Sale has not started");
    if (!publicSaleOn){
      require(IBEP20(jedCoin).balanceOf(msg.sender) >= jedCoinPresaleMin * (10 ** IBEP20(jedCoin).decimals()), "You need to have the minimum number of JED tokens required to mint during the presale");
      require(totalPerAccount[_to] < presaleBuyLimit, "This account has exceeded the maximum allowance for presale");
    }

    require(_productId < allProds.length, "Invalid character ID reference");
    require(_tierId < allProds[_productId].mintLimits.length, "Invalid tier ID reference");
    require(coinCostMultiplier[_coin] > 0, "Requested coin is not supported");

    require(allProds[_productId].totalMints[_tierId] < allProds[_productId].mintLimits[_tierId] , "The maximum number has been minted for this category");

    //Transfer the specified currency and amount to the owner wallet
    uint256 coinCost = allProds[_productId].baseMintCostPerTier[_tierId] * coinCostMultiplier[_coin];
    IBEP20(_coin).transferFrom(
      msg.sender,
      owner(),
      coinCost
    );
    uint256 newItemId = _counter;
    //Define the unique properties of this NFT and write it
    Item memory myItem;
    myItem.productIndex = _productId;
    myItem.productTier = _tierId;
    myItem.propertyInt = uint256(0);
    myItem.lastGenTime = block.timestamp;
    tokenIdToItem[newItemId] = myItem;

    //Mint the NFT with no characteristics first
    allProds[_productId].totalMints[_tierId]++;
    _mint(_to, newItemId);
    _setTokenURI(newItemId, string(abi.encodePacked("https://nft.jedstar.space/jednft1-",Strings.toString(newItemId),".json")));

    //Request for randomness
    uint256 vrfReqId = VRFCoordinator.requestRandomWords(
      keyHash,
      chainlinkVRFsubscriptionId,
      requestConfirmations,
      callbackGasLimit,
      numWords
    );
    //maintain a mapping between this randomness request and the token ID
    vrfIdToTokenId[vrfReqId] = newItemId;

    _counter++;

    return newItemId;
   }

   //ChainLink callback receiver
   function fulfillRandomWords(
     uint256 _requestId,
     uint256[] memory randomWords
   ) internal override {
       require (vrfIdToTokenId[_requestId] > 0, "VRF Request ID not found");
       require (randomWords.length > 0, "VRF did not return the expected number of words");
      //Now that the randomness has returned, store the word
      tokenIdToItem[vrfIdToTokenId[_requestId]].propertyInt = randomWords[0];
      //Store this VRF ID with the token for audit trail
      tokenIdToVRFs[vrfIdToTokenId[_requestId]].push(_requestId);
      //Purge the VRF ID token so the transaction can't be replayed with different figures
      vrfIdToTokenId[_requestId] = 0;
   }

   //Override token transfer hook to manage information related to who owns
   //which token
   function _afterTokenTransfer(
       address from,
       address to,
       uint256 tokenId
   ) internal virtual override {
     //handle any necessary steps from the inherited fn
     super._afterTokenTransfer(from, to, tokenId);

     //now update the mappings to store who owns what
     if (from != address(0)){
       _tokensByOwner[from] = removeMatchingElemFromUIntArray(tokenId, _tokensByOwner[from]);
     }
     if (to != address(0)){
       _tokensByOwner[to].push(tokenId);
     }
   }

  //Total supply fn required for scan UIs
  function totalSupply() external view returns (uint256) {
    return _counter - 1;
  }

  /**************************************
   *** Custom functions for JED NFTs ****
   **************************************/
   //===== GETTERS =======

  //Return all known VRF IDs that a token has made use of
  function getTokenVRFs(uint256 _tokenId) tokenExists(_tokenId) public view returns (uint256[] memory) {
      return tokenIdToVRFs[_tokenId];
  }

  function getTokenIdsByWallet(address _walletAddress) override external view returns (uint256[] memory){
    return _tokensByOwner[_walletAddress];
  }
  //Get the total number of available products
  function getTotalProducts() public view returns (uint256) {
    return allProds.length;
  }
  //Provide functions to query product details
  //Product name
  function getProductNameFromIndex(uint8 _idx) public view returns (string memory){
    require(_idx < allProds.length, "Product index not recognised");
    return allProds[_idx].productName;
  }
  //    uint16 productType;
  //  uint256[5] mintLimits;
  //  uint256[5] totalMints;
  //  uint256[5] baseMintCostPerTier; //assume price in cents

  //Product type
  function getProductTypeFromIndex(uint8 _idx) public view returns (uint16){
    require(_idx < allProds.length, "Product index not recognised");
    return allProds[_idx].productType;
  }
  //Product mint limits
  function getProductMintLimitsFromIndex(uint8 _idx) public view returns (uint256[5] memory){
    require(_idx < allProds.length, "Product index not recognised");
    return allProds[_idx].mintLimits;
  }
  //Product total mints
  function getProductTotalMintsFromIndex(uint8 _idx) public view returns (uint256[5] memory){
    require(_idx < allProds.length, "Product index not recognised");
    return allProds[_idx].totalMints;
  }
  //Number of NFTs minted per character x tier
  function getMintCount(uint16 _productId, uint8 _tierId) public view returns (uint256){
    require(_productId < allProds.length, "Product ID not recognised");
    require(_tierId < allProds[_productId].mintLimits.length, "Tier ID not recognised");
    return (allProds[_productId].totalMints[_tierId]);
  }
  //Base mint cost per tier
  function getProductBaseCostsFromIndex(uint8 _idx) public view returns (uint256[5] memory){
    require(_idx < allProds.length, "Product index not recognised");
    return allProds[_idx].baseMintCostPerTier;
  }
  //Get cost based on coin
  function getProductCostPerTier (
    uint256 _productId,
    address _coin
  ) public view returns (uint256[5] memory) {
      require (coinCostMultiplier[_coin] > 0, "The requested coin is not supported");
      require (allProds.length > _productId, "Product ID does not exist");
      //TODO
      uint256[5] memory costPerTier;
      for (uint8 i = 0; i < 5; i++){
        costPerTier[i] = allProds[_productId].baseMintCostPerTier[i] * coinCostMultiplier[_coin];
      }
      return costPerTier;
  }
  function getRegenCostByCoin(address _coin) override public view returns (uint256){
    return supportedCoinRegenCost[_coin];
  }
  function getItemIndex(uint256 _tokenId) tokenExists(_tokenId) override external view returns (uint256) {
    return tokenIdToItem[_tokenId].productIndex;
  }
  function getItemTier(uint256 _tokenId) tokenExists(_tokenId) override external view returns (uint8) {
    return tokenIdToItem[_tokenId].productTier;
  }
  function getItemProperties(uint256 _tokenId) tokenExists(_tokenId) override external view returns (uint256[] memory) {
    uint16 totalProps = 0;
    uint16 propsCompleted = 0;
    for (uint16 k = 0; k < itemProps.length; k++){
      totalProps += itemProps[k].propertyCount;
    }
    bool[] memory assigned = new bool[](totalProps);
    uint256[] memory _fullItemProperties = new uint256[](totalProps);

    if (tokenIdToItem[_tokenId].propertyInt == 0){
      //If the random number has not been set, there is nothing to compute
      return _fullItemProperties;
    }

    for (uint16 j = 0; j < itemProps.length; j++){
      uint16 props = itemProps[j].tierFreeAssigns[tokenIdToItem[_tokenId].productTier];
      uint16 i = 0;

      while (props > 0 && i < propertyIterationMax){
          uint256 arrPos = uint256(propsCompleted) + uint256(tokenIdToItem[_tokenId].propertyInt/(10 ** i)) % itemProps[j].propertyCount;
          uint256 random_offset = generateOffsetVar(_tokenId, i);
          if (!assigned[arrPos]){
              assigned[arrPos] = true;
              _fullItemProperties[arrPos] = (uint256(tokenIdToItem[_tokenId].propertyInt/(10 ** ((i + propsCompleted + random_offset) % 70))) % 100) + 1;
              props--;
          }
          i++;
      }
      propsCompleted += itemProps[j].propertyCount;
    }
    return _fullItemProperties;
  }
  function generateOffsetVar(uint256 _tokenId, uint16 i) private view returns (uint256){
    return uint256(tokenIdToItem[_tokenId].propertyInt/(10 ** (i+1))) % 10 + uint256(tokenIdToItem[_tokenId].propertyInt/(10 ** (i+2))) % 10;
  }
  function getItemExtra(uint256 _tokenId) tokenExists(_tokenId) override external view returns (string memory){
    return tokenIdToItem[_tokenId].extraData;
  }
  //========= SETTERS =========
  //---- Public / User / NFT Owner ----
  /**
   * @dev Regenerates the character properties if the NFT owner has enough JED
   *
   * Requirements:
   *
   * - There is a minimum time between regens which is based on how much JED is held by the user.
   *
   **/
  function regenProperties(uint256 _tokenId, address _coin) tokenExists(_tokenId) override external {
    require(msg.sender == ownerOf(_tokenId), "You must be the owner of the item to use this function");
    require(supportedCoinRegenCost[_coin] > 0, "The requested coin is not supported by this function");
    require(block.timestamp - tokenIdToItem[_tokenId].lastGenTime > 3 days, "You must wait at least 3 days before you can regen your item");

    uint256 timeDiff = block.timestamp - tokenIdToItem[_tokenId].lastGenTime;
    uint256 jedBalance = IBEP20(jedCoin).balanceOf(msg.sender);

    if (timeDiff > 30 days){
      require(jedBalance >= jedRegenReq[0], "You do not have the minimum JED tokens to regen your item");
    }
    if (timeDiff <= 30 days){
      require(jedBalance >= jedRegenReq[1], "You do not have the minimum JED tokens to regen your item under 30 days");
    }
    if (timeDiff <= 14 days){
      require(jedBalance >= jedRegenReq[2], "You do not have the minimum JED tokens to regen your item under 14 days");
    }
    if (timeDiff <= 7 days){
      require(jedBalance >= jedRegenReq[3], "You do not have the minimum JED tokens to regen your item under 7 days");
    }

    //Bill in the supported coin for this fn to recover VRF costs
    uint256 coinDecimals = 10**IBEP20(_coin).decimals();
    IBEP20(_coin).transferFrom(
      msg.sender,
      owner(),
      supportedCoinRegenCost[_coin] * coinDecimals
    );
    //Request for randomness
    uint256 vrfReqId = VRFCoordinator.requestRandomWords(
      keyHash,
      chainlinkVRFsubscriptionId,
      requestConfirmations,
      callbackGasLimit,
      numWords
    );
    //maintain a mapping between this randomness request and the token ID
    vrfIdToTokenId[vrfReqId] = _tokenId;

    tokenIdToItem[_tokenId].lastGenTime = block.timestamp;
  }
  //Check when the last (re)generation time was
  function getItemLastGenTime(uint256 _tokenId) tokenExists(_tokenId) override external view returns (uint256) {
    return tokenIdToItem[_tokenId].lastGenTime;
  }

  //---- Trusted Users ----
  //Allow for approved contracts to add additional information to the NFT
  function setItemExtra(uint256 _tokenId, string memory _extraData) tokenExists(_tokenId) isTrusted override external {
    tokenIdToItem[_tokenId].extraData = _extraData;
  }


  //---- Owner / Admin ----
  //Settings manager for Chainlink VRF
  function updateChainlinkVRFSettings (
   bytes32 _keyHash,
   uint256 _fee,
   uint16 _requestConfirmations,
   uint32 _callbackGasLimit,
   uint32 _numWords,
   uint64 _vrfSubscriptionId
  ) public onlyOwner{
   keyHash = _keyHash;
   fee = _fee;
   requestConfirmations = _requestConfirmations;
   callbackGasLimit = _callbackGasLimit;
   numWords = _numWords;
   chainlinkVRFsubscriptionId = _vrfSubscriptionId;
  }

  //Facility to add completely new products for sale
  function addNewProduct(
    string memory _prodName,
    uint16 _productType,
    uint256[5] memory _mintLimit,
    uint256[5] memory _baseCostPerTier
  ) public onlyOwner returns (uint256){
    Products memory newProd;
    newProd.productName = _prodName;
    newProd.productType = _productType;
    newProd.mintLimits = _mintLimit;
    newProd.totalMints = [uint256(0), 0,0,0,0];
    newProd.baseMintCostPerTier = _baseCostPerTier;
    allProds.push(newProd);
    return allProds.length - 1;
  }
  /**
    * @dev Allows contract owner to specify the cost of each NFT tier for a specific product.
    *
    * Requirements:
    *
    * - '_productId' must already exist
    * - `_tierCostsBaseLine` must be an array of ints 5 in length greater than zero.
    *
    */
  function setProductCostPerTier (
    uint256 _productId,
    uint256[5] memory _tierCostsBaseline
  ) public onlyOwner{
    require ( _tierCostsBaseline.length == 5, "There must be exactly 5 elements in the array");
    for (uint8 i = 0; i < 5; i++){
      require(_tierCostsBaseline[i] > 0, "Tier costs must be greater than zero");
    }
    require(allProds.length > _productId, "Product ID does not exist");

    allProds[_productId].baseMintCostPerTier = _tierCostsBaseline;
  }

  function setJEDToken (address _jedCoin) public onlyOwner{
      jedCoin = _jedCoin;
  }
  function setKREDToken (address _kredCoin) public onlyOwner{
      kredCoin = _kredCoin;
  }
  function setAcceptedCoins (address[] memory _coins, uint256[] memory _costs) public onlyOwner{
    require (_coins.length > 0, "At least one coin must be specified");
    require (_coins.length == _costs.length, "You must provide costs for each coin that will be supported");
    for (uint16 i = 0; i < _coins.length; i++){
        coinCostMultiplier[_coins[i]] = _costs[i];
    }
  }
  function setRejectedCoins (address[] memory _coins) public onlyOwner{
    require(_coins.length > 0, "At least one coin must be specified");
    for (uint16 i = 0; i < _coins.length; i++){
        //set the cost to zero as this will result in the coin mint being rejected
        coinCostMultiplier[_coins[i]] = 0;
    }
  }

  // Provide methods to extract tokens accidentally transferred to the contract
  function withdrawBNB(uint256 _value) external onlyOwner {
    payable(owner()).transfer(_value);
  }
  function withdrawAltCoin(address _coin, uint256 _value) external onlyOwner {
    IBEP20(_coin).transfer(owner(), _value);
  }

  // Provide owner ways to start and stop sales from the contract
  function setSale(bool preIsOn, bool publicIsOn) public onlyOwner{
    preSaleOn = preIsOn;
    publicSaleOn = publicIsOn;
  }

  // Allow owner to cap pre-sale purchase volumes
  function setPresaleBuyLimit(uint16 _bl) public onlyOwner{
    presaleBuyLimit = _bl;
  }
  function setRegenCostByCoin(address _coin, uint256 _cost) public onlyOwner{
    supportedCoinRegenCost[_coin] = _cost;
  }
  function increasePropertyCount(uint16 _propCountInc, uint16[5] memory _freePropsByTier) public onlyOwner {
    uint16 totalProps = 0;
    for (uint16 k = 0; k < itemProps.length; k++){
      totalProps += itemProps[k].propertyCount;
    }
    require(totalProps + _propCountInc < 99, "Total number of properties cannot exceed 99");
    for (uint16 k = 0; k < 5; k++){
      require(_freePropsByTier[k] <= _propCountInc, "Free props can not exceed number of props being added");
    }
    ItemProperties memory _newProps;
    _newProps.propertyCount = _propCountInc;
    _newProps.tierFreeAssigns = _freePropsByTier;
    itemProps.push(_newProps);
  }
  function setPropertyIterationMax(uint16 _itMax) public onlyOwner {
    require(_itMax < 70, "Number of iterations cannot exceed 70");
    propertyIterationMax = _itMax;
  }
  function setJedCoinPresaleMin(uint256 _minJed) public onlyOwner {
    jedCoinPresaleMin = _minJed;
  }
  function addTrustedAddress(address _newTrusted) public onlyOwner {
    require(trustedWallets.length < 10, "Too many addresses in the trusted list. Remove some before adding new.");
    trustedWallets.push(_newTrusted);
  }
  function removeTrustedAddress(address _toRemove) public onlyOwner {
    require(trustedWallets.length > 0, "No addresses left to remove.");
    if (trustedWallets.length == 1){
      if (trustedWallets[0] == _toRemove){
        delete trustedWallets;
      }else{
        revert("Address is not currently trusted");
      }
    }else{
      bool found;
      uint8 r;
      for (uint8 i = 0; i < trustedWallets.length && !found; i++){
        if (trustedWallets[i] == _toRemove){
          found = true;
          r = i;
        }
      }
      require(found, "Address is not currently trusted");
      address[] memory _newTrusts = new address[](trustedWallets.length - 1);
      uint8 j = 0;
      for (uint8 i = 0; i < trustedWallets.length; i++){
        if (i != r){
          _newTrusts[j] = trustedWallets[i];
          j++;
        }
      }
      trustedWallets = _newTrusts;
    }
  }

  function removeMatchingElemFromUIntArray(uint256 needle, uint256[] memory haystack) internal returns(uint256[] memory){
    uint256[] memory newHaystack;
    if (haystack.length == 1){
      if (haystack[0] == needle){
        delete haystack;
      }
    }else{
      bool found;
      uint8 r;
      for (uint8 i = 0; i < haystack.length && !found; i++){
        if (haystack[i] == needle){
          found = true;
          r = i;
        }
      }
      if (found){
        newHaystack = new uint256[](haystack.length - 1);
        uint8 j = 0;
        for (uint8 i = 0; i < haystack.length; i++){
          if (i != r){
            newHaystack[j] = haystack[i];
            j++;
          }
        }
      }
    }
    return newHaystack;
  }
}
