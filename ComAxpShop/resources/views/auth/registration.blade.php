<main class="signup-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-header text-center">Register User</h3>
                    <div class="card-body">

                        <form action="{{ route('register.custom') }}" method="POST">
                            @csrf
                                <div class="form-input">
                                    <label>employeeNumber</label>
                                    <input type="text" name="employeeNumber">
                                </div>
                                <div class="form-input">
                                    <label>lastName</label>
                                    <input type="text" name="lastName">
                                </div>
                                <div class="form-input">
                                    <label>firstName</label>
                                    <input type="text" name="firstName">
                                </div>
                                <div class="form-input">
                                    <label>extension</label>
                                    <input type="text" name="extension">
                                </div>
                                <div class="form-input">
                                    <label>email</label>
                                    <input type="text" name="email">
                                </div>
                                <div class="form-input">
                                    <label>officeCode</label>
                                    <input type="text" name="officeCode">
                                </div>
                                <div class="form-input">
                                    <label>reportsTo</label>
                                    <input type="text" name="reportsTo">
                                </div>
                                <div class="form-input">
                                    <label>jobTitle</label>
                                    <input type="text" name="jobTitle">
                                </div>
                                <div class="form-input">
                                    <label>password</label>
                                    <input type="text" name="password">
                                </div>
                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-dark btn-block">Sign up</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>