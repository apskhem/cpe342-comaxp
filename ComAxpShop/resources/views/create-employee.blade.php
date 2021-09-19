<!doctype html>

<html lang ="{{app() -> getLocale()}}">
    <head>
        <title> Create Product | Product Store </title>
    </head>
    <body>
        <div class="links">
            <a href="{{ config('app.url') }}">Home</a>
        </div>
        <div class="content">
            <form method="POST" action="{{ config('app.url') }}/employees">
                @csrf
                <h1> Enter Details to create a new employee</h1>
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
                <button type="text"> Submit </button>
            </form>
        </div>
    </body>
</html>