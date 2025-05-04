<?php $pageTitle = 'Login'; ?>

<!-- This code displays the "Login" page where users can enter their username and password to log in.
  The form includes input fields for the username and password, with a toggle button to reveal or hide the password. 
  After the user submits the form, it sends the login data to the "doLogin" action of the user page.
   A link to the registration page is also provided for users who do not have an account. -->


<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <form action="index.php?page=user&action=doLogin" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="index.php?page=user&action=register">Register</a></p>
            </div>
        </div>
    </div>
</div>