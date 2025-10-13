# 📁 Image Assets Guide for POS Ecoretech

## Directory Structure

### 1. Static Images (Public Assets)
**Location**: `public/images/`

```
public/images/
├── logo/
│   ├── logo.png                    # Main logo
│   ├── logo-dark.png              # Dark theme logo
│   ├── logo-light.png             # Light theme logo
│   └── favicon.ico                # Website favicon
├── icons/
│   ├── dashboard-icon.svg         # Dashboard icon
│   ├── user-icon.png              # User profile icon
│   ├── settings-icon.png          # Settings icon
│   ├── product-icon.png           # Product icon
│   ├── order-icon.png             # Order icon
│   ├── payment-icon.png           # Payment icon
│   └── delivery-icon.png          # Delivery icon
├── placeholders/
│   ├── no-image.png               # No image placeholder
│   ├── product-placeholder.jpg    # Product placeholder
│   ├── user-avatar.png            # User avatar placeholder
│   └── customer-placeholder.jpg   # Customer placeholder
└── backgrounds/
    ├── login-bg.jpg               # Login background
    ├── dashboard-bg.jpg           # Dashboard background
    └── receipt-bg.jpg             # Receipt background
```

### 2. User-Uploaded Images (Storage)
**Location**: `storage/app/public/`

```
storage/app/public/
├── products/
│   ├── 2024/
│   │   ├── 01/                    # January 2024
│   │   │   ├── product-001.jpg
│   │   │   └── product-002.png
│   │   ├── 02/                    # February 2024
│   │   └── 12/                    # December 2024
│   └── 2023/
├── customers/
│   ├── customer-001.jpg
│   ├── customer-002.png
│   └── customer-003.jpg
├── employees/
│   ├── employee-001.jpg
│   ├── employee-002.png
│   └── employee-003.jpg
├── quotations/
│   ├── quotation-001.pdf
│   ├── quotation-002.pdf
│   └── quotation-003.pdf
├── receipts/
│   ├── receipt-001.png
│   ├── receipt-002.png
│   └── receipt-003.png
└── temp/                          # Temporary uploads
    ├── temp-001.jpg
    └── temp-002.png
```

## Usage in Blade Templates

### Static Images
```php
<!-- Logo -->
<img src="{{ asset('images/logo/logo.png') }}" alt="Ecoretech Logo" class="logo">

<!-- Icons -->
<img src="{{ asset('images/icons/dashboard-icon.svg') }}" alt="Dashboard" class="icon">

<!-- Placeholders -->
<img src="{{ asset('images/placeholders/no-image.png') }}" alt="No Image" class="placeholder">

<!-- Backgrounds -->
<div class="login-bg" style="background-image: url('{{ asset('images/backgrounds/login-bg.jpg') }}')">
```

### User-Uploaded Images
```php
<!-- Products -->
<img src="{{ Storage::url('products/2024/01/product-001.jpg') }}" alt="Product Image">

<!-- Alternative method -->
<img src="{{ asset('storage/products/2024/01/product-001.jpg') }}" alt="Product Image">

<!-- With fallback -->
<img src="{{ $product->image ? Storage::url($product->image) : asset('images/placeholders/product-placeholder.jpg') }}" alt="Product">

<!-- Customers -->
<img src="{{ Storage::url('customers/customer-001.jpg') }}" alt="Customer Photo">

<!-- Employees -->
<img src="{{ Storage::url('employees/employee-001.jpg') }}" alt="Employee Photo">
```

## File Upload Handling

### Controller Example
```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

public function uploadProductImage(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = 'product-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('products/' . date('Y/m'), $filename, 'public');
        
        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path)
        ]);
    }
}
```

### Model Example
```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = ['name', 'image', 'price'];
    
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : asset('images/placeholders/product-placeholder.jpg');
    }
}
```

## Image Optimization Recommendations

### 1. Image Sizes
- **Logo**: 200x200px (PNG with transparency)
- **Icons**: 24x24px or 32x32px (SVG preferred)
- **Product Images**: 400x400px (JPEG/PNG)
- **User Avatars**: 150x150px (JPEG/PNG)
- **Backgrounds**: 1920x1080px (JPEG)

### 2. File Formats
- **Logos**: PNG (with transparency) or SVG
- **Icons**: SVG (scalable) or PNG
- **Photos**: JPEG (smaller file size)
- **Screenshots**: PNG (better quality)
- **Favicon**: ICO format

### 3. File Naming Convention
- **Static Images**: `kebab-case` (e.g., `dashboard-icon.svg`)
- **User Uploads**: `type-id-timestamp.ext` (e.g., `product-001-1640995200.jpg`)

## Storage Link Setup

Make sure to create the storage link for public access:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

## Security Considerations

1. **File Validation**: Always validate file types and sizes
2. **File Naming**: Use unique filenames to prevent conflicts
3. **Access Control**: Protect sensitive images with proper permissions
4. **Virus Scanning**: Consider implementing virus scanning for uploads

## Performance Tips

1. **Image Compression**: Compress images before upload
2. **Lazy Loading**: Implement lazy loading for large image lists
3. **CDN**: Consider using a CDN for better performance
4. **Caching**: Implement proper caching headers

## Example Implementation

### Product Image Upload
```php
// In your ProductController
public function store(Request $request)
{
    $data = $request->validated();
    
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = 'product-' . time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('products/' . date('Y/m'), $imageName, 'public');
        $data['image'] = $imagePath;
    }
    
    Product::create($data);
    
    return redirect()->route('products.index')->with('success', 'Product created successfully!');
}
```

### Display Product Images
```php
<!-- In your Blade template -->
@foreach($products as $product)
    <div class="product-card">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
        <h3>{{ $product->name }}</h3>
        <p>₱{{ number_format($product->price, 2) }}</p>
    </div>
@endforeach
```

This guide provides a complete structure for managing image assets in your POS system!
