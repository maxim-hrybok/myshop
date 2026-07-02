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
        if ($product && !empty($product['image_url']) && strpos($product['image_url'], '/') !== 0) {
            $this->imageService->deleteImages($product['image_url']);
        }
        $this->productRepo->deleteProduct($id);
    }

    private function validateProductData(array $data): void {
        if (empty(trim($data['name'] ?? '')) || (float)($data['price'] ?? 0) <= 0 || 
            (int)($data['stock'] ?? -1) < 0 || (int)($data['discount'] ?? -1) < 0 || 
            (int)($data['discount'] ?? 0) > 100) {
            throw new Exception("Invalid product data provided. Name is required, and price must be greater than 0.");
        }
    }
}