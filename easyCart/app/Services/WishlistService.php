<?php

namespace Services;

/**
 * Wishlist Service
 * Handles wishlist business logic (file-based for now)
 */
class WishlistService {
    
    /**
     * Get wishlist file path
     * @param string $userId
     * @return string
     */
    private function getWishlistFilePath($userId) {
        $dataDir = STORAGE_PATH . '/wishlist';
        if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
        return $dataDir . '/wishlist_' . md5($userId) . '.json';
    }
    
    /**
     * Load user wishlist
     * @param string $userId
     * @return array
     */
    public function loadUserWishlist($userId) {
        $wishlistFile = $this->getWishlistFilePath($userId);
        if (file_exists($wishlistFile)) {
            $content = file_get_contents($wishlistFile);
            $data = json_decode($content, true);
            return is_array($data) ? $data : [];
        }
        return [];
    }
    
    /**
     * Save user wishlist
     * @param string $userId
     * @param array $wishlist
     */
    public function saveUserWishlist($userId, $wishlist) {
        $wishlistFile = $this->getWishlistFilePath($userId);
        file_put_contents($wishlistFile, json_encode($wishlist, JSON_PRETTY_PRINT));
    }
    
    /**
     * Merge guest wishlist
     * @param array $guestWishlist
     */
    public function mergeGuestWishlist($guestWishlist) {
        if (!empty($guestWishlist) && isset($_SESSION['user_email'])) {
            $userWishlist = $this->loadUserWishlist($_SESSION['user_email']);
            
            foreach ($guestWishlist as $productId => $guestWishItem) {
                if (!isset($userWishlist[$productId])) {
                    $userWishlist[$productId] = $guestWishItem;
                }
            }
            
            $_SESSION['wishlist'] = $userWishlist;
            $this->saveUserWishlist($_SESSION['user_email'], $userWishlist);
        } else if (isset($_SESSION['user_email'])) {
            $_SESSION['wishlist'] = $this->loadUserWishlist($_SESSION['user_email']);
        }
    }
}
