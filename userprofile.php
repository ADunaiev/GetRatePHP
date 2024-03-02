
<?php if($user == null)
{ ?>
    <div class="row">
        <div class="col s12 m6">
            <div class="card red lighten-2">
                <div class="card-content white-text">
                    User are not found
                </div>
            </div>
        </div>
    </div>
<?php }
else
{ ?>
  <form class="col s12 m7" id="user-profile-form">
    <h2 class="header">User profile</h2>
    <div class="card horizontal">
      <div class="card-image">
        <img src="/avatar/<?= $user['avatar'] ?>">
      </div>
      <div class="card-stacked">
        <div class="card-content">
        <div class="row">
        <div class="col s12">
            <div class="row">

                <div class="input-field col s12" style="display: none">
                    <input hidden value="<?= $user['id'] ?>" id="profile-id" type="text" class="validate">
                    <label for="profile-id">User id</label>
                </div>

                <div class="input-field col s12 m12">
                    <input id="profile-name" name="profile-name" 
                           type="text" class="validate"
                           value="<?= $user['name'] ?>">
                    <label for="profile-name">Name</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Use only letters</span>
                </div>
                
                <div class="input-field col s12 m12">
                    <input id="profile-email" name="profile-email" 
                           type="email" class="validate"
                           value="<?= $user['email'] ?>">
                    <label for="profile-email">Email</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Email</span>
                </div>

                <div class="input-field col s12 m12">
                    <input id="profile-password" name="profile-password" type="password" class="validate">
                    <label for="profile-password">New password</label>
                    <span class="helper-text" data-error="wrong" data-success="right">New password</span>
                </div>

                <div class="input-field col s12 m12">
                    <input id="profile-repeat" name="profile-repeat" type="password" class="validate">
                    <label for="profile-repeat">Repeat password</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Repeat password</span>
                </div>

                <div class="file-field input-field col s12 m12">
                    <div class="btn">
                        <span>Avatar</span>
                        <input id="profile-avatar-file" name="profile-avatar" type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input  id="profile-avatar-file-path" class="file-path validate" type="text">
                    </div>
                </div>

            </div>  

        </div>

    </div>
        </div>
        <div class="card-action center">
          <a href="#" id="profile-update-btn" class="btn cyan darken-1 white-text">Update</a>
        </div>
      </div>
    </div>
  </form>
<?php } ?>