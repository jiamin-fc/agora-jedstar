// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

/**
 * Jedstar NFT interface definition
 *
 * Jedstar NFTs are utility NFTs that support data being written on chain to
 * enable and support that utility. This interface defines the functions that
 * need to be available for the various utility that the NFTs afford.
 *
 **/

interface JedstarNFT {
  /**
   * @dev Support discovery of user NFTs by wallet address
   */
  function getTokenIdsByWallet(address _walletAddress) external view returns (uint256[] memory);

  /**
   * @dev There are multiple different items that are available, this method
   * allows for the specific to be identified from the token ID
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

   * @dev Each item supports additional data being associated with it, this
   * method allows that data to be retrieved
   */
  function getItemExtra(uint256 _tokenId) external view returns (string memory);

  /**
   * @dev Enables the extra data field to be updated for a specific NFT
   */
  function setItemExtra(uint256 _tokenId, string memory _extraData) external;

  /**
   * @dev Allows the randomised properties to within the NFT to be regenerated
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
