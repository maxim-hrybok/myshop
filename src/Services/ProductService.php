<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;

class ProductService {
    private ProductRepository $productRepo;
    private ImageService $imageService;

    public function __construct(ProductRepository $productRepo, ImageService $imageService) {
        $this->productRepo = $productRepo;
        $this->imageService = $imageService;
    }

    public function storeProduct(array $data, ?array $file): void {
        $this->validateProductData($data);

        $imgFilename = '';
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $imgFilename = $this->imageService->handleUpload($file);
        }

        $this->productRepo->createProduct(
            $data['name'], (float)$data['price'], (int)($data['discount'] ?? 0), 
            (int)$data['stock'], $data['description'] ?? '', $imgFilename, 
            $data['status'] ?? 'available', $data['categories'] ?? []
        );
    }

    public function updateProduct(int $id, array $data, ?array $file): void {
        $this->validateProductData($data);
        $imgFilename = $data['existing_image_url'] ?? '';

        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $newImgFilename = $this->imageService->handleUpload($file);
            
            // Delete old images
            if (!empty($imgFilename) && strpos($imgFilename, '/') !== 0) {
                $this->imageService->deleteImages($imgFilename);
            }
            $imgFilename = $newImgFilename;
        }

        $this->productRepo->updateProduct(
            $id, $data['name'], (float)$data['price'], (int)($data['discount'] ?? 0), 
            (int)$data['stock'], $data['description'] ?? '', $imgFilename, 
            $data['status'] ?? 'available', $data['categories'] ?? []
        );
    }

   

    public function deleteProduct(int $id): void {
        $product = $this->productRepo->findProductById($id);
        if (!$product) {
            return;
        }

        try {
            // 1. Try to delete from the database FIRST
            $this->productRepo->deleteProduct($id);

            // 2. Only if the DB deletion succeeds, delete the physical images
            if (!empty($product['image_url']) && strpos($product['image_url'], '/') !== 0) {
                $this->imageService->deleteImages($product['image_url']);
            }
            
        } catch (\PDOException $e) {
            // Code 23000 means Foreign Key Constraint failed (Product is in an order)
            if ($e->getCode() == '23000') {
                throw new Exception("Cannot delete this product because it is linked to existing customer orders. Please edit it and set the status to 'Unavailable' instead.");
            }
            
            // If it's a different database error, throw a generic message
            throw new Exception("Database error occurred while trying to delete the product.");
        }
    }

    private function validateProductData(array $data): void {
        if (empty(trim($data['name'] ?? '')) || (float)($data['price'] ?? 0) <= 0 || 
            (int)($data['stock'] ?? -1) < 0 || (int)($data['discount'] ?? -1) < 0 || 
            (int)($data['discount'] ?? 0) > 100) {
            throw new Exception("Invalid product data provided. Name is required, and price must be greater than 0.");
        }
    }
}