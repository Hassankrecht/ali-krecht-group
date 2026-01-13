<form method="GET" action="{{ url()->current() }}" class="category-filter">
    <select name="category" onchange="this.form.submit()" class="form-control">
        <option value="">{{ __('All Categories') }}</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
</form>