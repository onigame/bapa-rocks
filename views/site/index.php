<?php

/* @var $this yii\web\View */

$this->title = 'BAPA Manager';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>BAPA Rocks!</h1>

        <p class="lead">The new BAPA Manager will eventually be here...</p>

<?php      
        if (Yii::$app->user->isGuest) {
?>
        <p> Please Sign In or Sign Up using the buttons above.  You can either sign up using 
         a Google or Facebook account, or create an account for use on this server only.
         If you do the account on this server only, do NOT use a sensitive password that
         you use on other sites.
       </p>

        <p>Did you sign in but miss the confirmation window?  Request another one 
          <a class="btn btn-success" href="/user/registration/resend">here</a>.
        </p>
   
<?php      
        } else {
?>
        <p> Thanks for Signing in!  Now please enter your real name into
          <a class="btn btn-success" href="/user/settings/profile">your profile</a>.
<?php      
        };
?>

<!--
        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
-->
    </div>

<!--
    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
-->
</div>
