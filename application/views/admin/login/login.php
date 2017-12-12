    
    <div class="container">
    <div class="admin-login">
    <div class="text-center">
        <img src="<?php echo site_url();?>assets/admin/images/logo-new.png" id="" alt=" Logo" height="62" width="150" />
    </div>
    <div class="tab-content">
        <div id="login" class="tab-pane active">
            <?php
                 if(validation_errors()):
                  echo validation_errors();
                 endif; 
            ?>
            <form action="" class="form-signin" name="login" method="post">
                
                <input type="text" placeholder="Email" name="email" class="form-control" />
                <input type="password" placeholder="Password" name="password" class="form-control" />
                <button class="btn text-muted text-center btn-danger" type="submit">Sign in</button>
            </form>
        </div>
        <div id="forgot" class="tab-pane">
            <form action="index.html" class="form-signin">
                <p class="text-muted text-center btn-block btn btn-primary btn-rect">Enter your valid e-mail</p>
                <input type="email"  required="required" placeholder="Your E-mail"  class="form-control" />
                <br />
                <button class="btn text-muted text-center btn-success" type="submit">Recover Password</button>
            </form>
        </div>
        <div id="signup" class="tab-pane">
            <form action="index.html" class="form-signin">
                <p class="text-muted text-center btn-block btn btn-primary btn-rect">Please Fill Details To Register</p>
                 <input type="text" placeholder="First Name" class="form-control" />
                 <input type="text" placeholder="Last Name" class="form-control" />
                <input type="text" placeholder="Username" class="form-control" />
                <input type="email" placeholder="Your E-mail" class="form-control" />
                <input type="password" placeholder="password" class="form-control" />
                <input type="password" placeholder="Re type password" class="form-control" />
                <button class="btn text-muted text-center btn-success" type="submit">Register</button>
            </form>
        </div>
    </div>
    </div>
    <div class="text-center">
        <ul class="list-inline">
            <!--
<li><a class="text-muted" href="#login" data-toggle="tab">Login</a></li>
            <li><a class="text-muted" href="#forgot" data-toggle="tab">Forgot Password</a></li>
            <li><a class="text-muted" href="#signup" data-toggle="tab">Signup</a></li>
-->
        </ul>
    </div>


</div>
