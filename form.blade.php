@csrf
@php
    $categoryOptions = ['Lumber', 'Hardware', 'Tools', 'Electrical', 'Plumbing', 'Paint'];
    $currentCategory = old('category', $product->category ?? '');
    $isCustomCategory = $currentCategory !== ''
        && !in_array($currentCategory, $categoryOptions, true)
        && $currentCategory !== 'Other';
    $selectedCategory = $isCustomCategory ? 'Other' : $currentCategory;
    $otherCategory = old('category_other', $isCustomCategory ? $currentCategory : '');
    $showOtherCategory = $selectedCategory === 'Other';
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Product Name</label>
        <input name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">SKU</label>
        <input name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <select id="category" name="category" class="form-select" required>
            <option value="">Select category</option>
            @foreach($categoryOptions as $category)
                <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
            @endforeach
            <option value="Other" @selected($showOtherCategory)>Other</option>
        </select>
    </div>
    <div id="other-category-field" class="col-md-6" @if(!$showOtherCategory) hidden @endif>
        <label for="category_other" class="form-label">Specify Category</label>
        <input id="category_other" name="category_other" class="form-control" maxlength="100"
            value="{{ $otherCategory }}" @if($showOtherCategory) required @endif>
    </div>
    <div class="col-md-6">
        <label class="form-label">Supplier</label>
        <input name="supplier" class="form-control" value="{{ old('supplier', $product->supplier ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Quantity</label>
        <input type="number" min="0" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity ?? 0) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Reorder Level</label>
        <input type="number" min="0" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level ?? 5) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Unit Price</label>
        <input type="number" min="0" step="0.01" name="unit_price" class="form-control" value="{{ old('unit_price', $product->unit_price ?? 0) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>
<script>
    const categorySelect = document.getElementById('category');
    const otherCategoryField = document.getElementById('other-category-field');
    const otherCategoryInput = document.getElementById('category_other');

    categorySelect.addEventListener('change', () => {
        const isOther = categorySelect.value === 'Other';
        otherCategoryField.hidden = !isOther;
        otherCategoryInput.required = isOther;

        if (!isOther) {
            otherCategoryInput.value = '';
        }
    });
</script>
