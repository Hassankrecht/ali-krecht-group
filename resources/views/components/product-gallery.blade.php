<div class="product-gallery">
    <div class="main-image">
        <img src="{{ $images[0] ?? '/assets/img/placeholder.png' }}" alt="Image">
    </div>
    <div class="thumbnails d-flex mt-2">
        @foreach($images as $img)
            <div class="thumb me-2"><img src="{{ $img }}" alt="thumb" class="img-thumbnail" style="width:60px;height:60px"></div>
        @endforeach
    </div>
</div>