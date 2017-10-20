<?php
include_once "../config.php";
$page_title = "Create User";
include_once "themes/{$theme}/header.phtml";
    <?php
    if (empty($messages) == false) :
        $error = $messages['Error'];
        ?>
        <div class="alert alert-danger alert-dismissable"><p><?= $error[0] ?></p></div>
    <?php endif; ?>

    <form class="form" action="<?php echo htmlspecialchars('/'); ?>" method="post">
        <div class="col-md-4">

            <div class="form-group ">
                <label class="control-label requiredField" for="name">
                    Password
                    <span class="asteriskField">
        *
       </span>
                </label>
                <input class="form-control" id="email" name="email" type="text"/>
            </div>

            <div class="form-group">
                <div>
                    <button class="btn btn-primary " name="submit" type="submit">
                        Submit
                    </button>
                </div>
            </div>
        </div>
   </form>
   <?php include_once 'themes/{$theme}/footer.phtml'; endif; ?>