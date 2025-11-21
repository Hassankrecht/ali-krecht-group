<div class="row g-3">
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" name="name" class="form-control akg-input" placeholder="Name" required>
            <label>Name</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
            <input type="email" name="email" class="form-control akg-input" placeholder="Email (optional)">
            <label>Email (optional)</label>
        </div>
    </div>

    {{-- address fields --}}
    @include('checkout.address_form')

    {{-- خيار إنشاء الحساب --}}
    <div class="form-check mt-3">
        <input class="form-check-input" type="checkbox" name="create_account" id="create_account" value="1">
        <label for="create_account" class="form-check-label text-light">
            I want to create an account for future orders
        </label>
    </div>

    <div id="passwordFields" class="row g-3 mt-2 d-none">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="password" name="password" class="form-control akg-input" placeholder="Password">
                <label>Password</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="password" name="password_confirmation" class="form-control akg-input"
                    placeholder="Confirm password">
                <label>Confirm Password</label>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const createAccount = document.getElementById('create_account');
    const passwordFields = document.getElementById('passwordFields');
    if (createAccount && passwordFields) {
        createAccount.addEventListener('change', () => {
            passwordFields.classList.toggle('d-none', !createAccount.checked);
        });
    }
});
</script>
