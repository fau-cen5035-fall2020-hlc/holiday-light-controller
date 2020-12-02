<section id="account" >
  <div class="container">
    <div class="row" >
      <div class="col-lg-8 mx-auto text-center" >
        <h2 class="section-heading">Account</h2>
        <hr class="my-4">
        <!--<div class="container col-md-3">-->

          <!-- Display error messages, if any  -->
          <?php require('displaymessages.php'); ?>

          <h5>User information</h5>

          <table class="table table-bordered">
            <tbody>
              <tr>
              <th scope="row">First name</th>
              <td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['fname']['stringValue']); ?></td>
              </tr>
              <tr>
              <th scope="row">Last name</th>
              <td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['lname']['stringValue']); ?></td>
              </tr>
              <tr>
              <th scope="row">User name</th>
              <td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['username']['stringValue']); ?></td>
              </tr>
              <tr>
              <th scope="row">Email address</th>
              <td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['email']['stringValue']); ?></td>
              </tr>
            </tbody>
          </table>
          <a href="update-profile.php">Update profile</a><br />
          <a href="change-password.php">Change password</a><br />
          <a href="login.php">Log out</a>
        <!--</div>-->
      </div>
    </div>
  </div>
</section>
