<?php
$pageTitle = $product ? 'Edit Product' : 'Add Product';
$activeNav = 'products';
include __DIR__ . '/../templates/market_vendor_layout.php';
?>

<style>
    .simple-form-card {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(212,165,116,.12);
        max-width: 800px;
        margin: 0 auto;
    }
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: .5rem;
        font-size: .95rem;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: .75rem 1rem;
        border: 2px solid #e5e5e5;
        border-radius: 8px;
        font-size: 1rem;
        transition: all .2s ease;
    }
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #D4A574;
        box-shadow: 0 0 0 3px rgba(212,165,116,.1);
    }
    .image-upload-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .image-upload-section label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: .75rem;
    }
    .current-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        margin-bottom: 1rem;
        object-fit: cover;
    }
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e5e5;
    }
    .btn-save {
        background: linear-gradient(135deg, #28a745 0%, #20903a 100%);
        color: white;
        padding: .75rem 2rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all .2s ease;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40,167,69,.3);
    }
    .btn-cancel {
        background: #6c757d;
        color: white;
        padding: .75rem 2rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all .2s ease;
    }
    .btn-cancel:hover {
        background: #5a6268;
        color: white;
    }
    @media (max-width: 768px) {
        .form-row-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="simple-form-card">
    <h4 class="mb-4" style="color: #333; font-weight: 700;">
        <?= $product ? 'Edit Product' : 'Add Product' ?>
    </h4>

    <form method="POST" action="index.php?action=saveProduct" enctype="multipart/form-data">
        <?php if ($product): ?>
            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['product_image'] ?? '') ?>">
        <?php endif; ?>

        <!-- Row 1: Name and Price -->
        <div class="form-row-2">
            <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="product_name" required
                       value="<?= htmlspecialchars($product['product_name'] ?? '') ?>"
                       placeholder="e.g., Talong (Eggplant)">
            </div>

            <div class="form-group">
                <label>Price (₱) <span class="text-danger">*</span></label>
                <input type="number" name="price_per_unit" required
                       step="0.01" min="0"
                       value="<?= htmlspecialchars($product['price_per_unit'] ?? '') ?>"
                       placeholder="0.00">
            </div>
        </div>

        <!-- Row 2: Stock and Category -->
        <div class="form-row-2">
            <div class="form-group">
                <label>Stock (pcs / kg) <span class="text-danger">*</span></label>
                <input type="text" name="stock_quantity" required
                       value="<?= htmlspecialchars($product['stock_quantity'] ?? '') ?>"
                       placeholder="e.g., 20 sack, 12 dozen, 50 kg">
            </div>

            <div class="form-group">
                <label>Category <span class="text-danger">*</span></label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php
                    $categories = ['Vegetables', 'Fruits', 'Meat', 'Fish', 'Rice & Grains', 
                                  'Dairy', 'Eggs', 'Spices', 'Condiments', 'Others'];
                    foreach ($categories as $cat):
                        $selected = ($product['category'] ?? '') === $cat ? 'selected' : '';
                    ?>
                        <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Hidden Unit Field (auto-set based on category) -->
        <input type="hidden" name="unit" value="<?= htmlspecialchars($product['unit'] ?? 'kg') ?>">

        <!-- Image Upload - Camera Only -->
        <div class="image-upload-section">
            <label>
                <i class="bi bi-camera-fill me-1"></i>Product Photo (Camera Only)
            </label>
            
            <?php if (!empty($product['product_image'])): ?>
                <div class="mb-3">
                    <img src="<?= htmlspecialchars($product['product_image']) ?>" 
                         alt="Current image" 
                         class="current-image"
                         id="existing-product-image">
                    <p class="text-muted small mb-2">Current image</p>
                </div>
            <?php endif; ?>
            
            <!-- Hidden file input - camera only -->
            <input type="file" id="product-image-file" style="display:none;" 
                   accept="image/*" capture="environment">
            <input type="hidden" id="product-image-data" name="product_image_data">
            
            <!-- Camera Interface -->
            <div id="camera-interface-product">
                <!-- Camera Button -->
                <div id="camera-button-product">
                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="startProductCamera()" style="padding:1rem;">
                        <i class="bi bi-camera-fill me-2" style="font-size:1.2rem;"></i>
                        <span style="font-size:1rem;font-weight:600;">Take Product Photo</span>
                    </button>
                    <small class="text-muted d-block text-center">
                        <i class="bi bi-info-circle me-1"></i>Camera will open to take a photo of your product
                    </small>
                </div>
                
                <!-- Live Camera View -->
                <div id="camera-view-product" style="display:none;">
                    <div class="position-relative">
                        <video id="camera-stream-product" autoplay playsinline style="width:100%; max-height:300px; border-radius:8px; background:#000;"></video>
                        <div class="position-absolute bottom-0 start-0 end-0 p-3 text-center" style="background:linear-gradient(transparent, rgba(0,0,0,0.7));">
                            <button type="button" class="btn btn-light btn-lg rounded-circle" onclick="captureProductPhoto()" style="width:60px; height:60px;">
                                <i class="bi bi-camera-fill" style="font-size:1.5rem;"></i>
                            </button>
                            <br>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="stopProductCamera()">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Photo Preview -->
                <div id="photo-preview-product" style="display:none;">
                    <div class="text-center mb-2">
                        <img id="photo-preview-img-product" src="" alt="Preview" class="img-fluid rounded" 
                             style="max-height: 250px; border: 3px solid #D4A574; box-shadow: 0 4px 12px rgba(0,0,0,.15);">
                        <canvas id="photo-canvas-product" style="display:none;"></canvas>
                    </div>
                    <button type="button" class="btn btn-warning w-100" onclick="retakeProductPhoto()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Retake Photo
                    </button>
                    <div class="alert alert-success mt-2 mb-0" style="padding:.6rem; font-size:.9rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Photo ready! You can now save the product.
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields for availability (default to available) -->
        <input type="hidden" name="is_available" value="1">
        <input type="hidden" name="description" value="">

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="index.php?action=vendorProducts" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">Save Product</button>
        </div>
    </form>
</div>

<script>
// Auto-set unit based on category
document.querySelector('select[name="category"]').addEventListener('change', function() {
    const unitField = document.querySelector('input[name="unit"]');
    const category = this.value;
    
    // Auto-suggest unit based on category
    if (['Vegetables', 'Fruits', 'Meat', 'Fish'].includes(category)) {
        unitField.value = 'kg';
    } else if (['Rice & Grains'].includes(category)) {
        unitField.value = 'sack';
    } else if (['Eggs'].includes(category)) {
        unitField.value = 'dozen';
    } else {
        unitField.value = 'pc';
    }
});

// ========================================================================
// CAMERA FUNCTIONALITY FOR PRODUCT PHOTOS
// ========================================================================

let productCameraStream = null;
let capturedProductBlob = null;

// Start camera
async function startProductCamera() {
    try {
        const constraints = {
            video: {
                facingMode: 'environment', // Back camera on mobile
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
        
        productCameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        const videoElement = document.getElementById('camera-stream-product');
        videoElement.srcObject = productCameraStream;
        
        // Show camera view
        document.getElementById('camera-button-product').style.display = 'none';
        document.getElementById('camera-view-product').style.display = 'block';
        
    } catch (error) {
        console.error('Camera error:', error);
        
        // Fallback to file input if camera API fails
        if (error.name === 'NotAllowedError') {
            alert('📸 Camera permission denied. Please allow camera access.');
        } else if (error.name === 'NotFoundError') {
            alert('📸 No camera found. Opening file picker as fallback...');
            document.getElementById('product-image-file').click();
            setupProductFileInputFallback();
        } else {
            alert('📸 Camera error: ' + error.message);
        }
    }
}

// Stop camera stream
function stopProductCamera() {
    if (productCameraStream) {
        productCameraStream.getTracks().forEach(track => track.stop());
        productCameraStream = null;
    }
    document.getElementById('camera-view-product').style.display = 'none';
    document.getElementById('camera-button-product').style.display = 'block';
}

// Capture photo from video stream
function captureProductPhoto() {
    const video = document.getElementById('camera-stream-product');
    const canvas = document.getElementById('photo-canvas-product');
    const preview = document.getElementById('photo-preview-img-product');
    
    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw current video frame to canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to blob
    canvas.toBlob((blob) => {
        capturedProductBlob = blob;
        
        // Convert to base64 for form submission
        const reader = new FileReader();
        reader.onloadend = function() {
            document.getElementById('product-image-data').value = reader.result;
        };
        reader.readAsDataURL(blob);
        
        // Show preview
        const url = URL.createObjectURL(blob);
        preview.src = url;
        
        // Hide existing image if any
        const existingImg = document.getElementById('existing-product-image');
        if (existingImg) {
            existingImg.style.display = 'none';
        }
        
        // Stop camera and show preview
        stopProductCamera();
        document.getElementById('camera-view-product').style.display = 'none';
        document.getElementById('photo-preview-product').style.display = 'block';
        
    }, 'image/jpeg', 0.85);
}

// Setup file input fallback
function setupProductFileInputFallback() {
    const fileInput = document.getElementById('product-image-file');
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            capturedProductBlob = file;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview-img-product').src = e.target.result;
                document.getElementById('product-image-data').value = e.target.result;
                document.getElementById('photo-preview-product').style.display = 'block';
                document.getElementById('camera-button-product').style.display = 'none';
                
                // Hide existing image
                const existingImg = document.getElementById('existing-product-image');
                if (existingImg) {
                    existingImg.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    };
}

// Retake photo
function retakeProductPhoto() {
    capturedProductBlob = null;
    document.getElementById('product-image-data').value = '';
    document.getElementById('photo-preview-product').style.display = 'none';
    document.getElementById('camera-button-product').style.display = 'block';
    
    // Show existing image again if any
    const existingImg = document.getElementById('existing-product-image');
    if (existingImg) {
        existingImg.style.display = 'block';
    }
}

// Clean up camera on page unload
window.addEventListener('beforeunload', function() {
    stopProductCamera();
});
</script>

<?php include __DIR__ . '/../templates/market_vendor_layout_end.php'; ?>
