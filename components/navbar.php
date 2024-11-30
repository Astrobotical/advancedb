<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$user  = $_SESSION['user'] ?? null;
$userIsLoggedIn = $_SESSION['userLoggedIn'] ?? null;
$userRole = $_SESSION['userRole'] ?? null;
?>

<div class="navbar bg-base-100">
  <div class="navbar-start">
    <!-- Mobile Dropdown (Visible only on mobile) -->
    <div class="dropdown lg:hidden md:block sm:block">
      <div tabindex="0" role="button" class="btn btn-ghost">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-primaryTextColor"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h8m-8 6h16" />
        </svg>
      </div>
      <ul
        tabindex="0"
        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
        <li><a href="/index.php" class="text-primaryTextColor hover:hover:bg-grey-500">Welcome Page</a></li>
        <li><a href="./pages/aboutus.php" class="text-primaryTextColor hover:hover:bg-grey-500">About Us</a></li>
        <li><a href="./pages/products.php" class="text-primaryTextColor hover:hover:bg-grey-500">Products</a></li>
        <li>
          <a class="text-sky-400 font-bold">Account</a>
          <ul class="p-2">
            <li><a class="text-primaryTextColor" href="/advancedb/pages/admin/products.php">Products</a></li>
            <li><a class="text-primaryTextColor">Settings</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <a class="btn btn-ghost text-xl text-primaryTextColor" href="/index.php">EcommerceJA</a>
  </div>

  <!-- Navbar Center (Visible only on desktop) -->
  <div class="navbar-center hidden lg:flex">
    <ul class="menu menu-horizontal px-1">
      <li><a href="/index.php" class="text-primaryTextColor hover:hover:bg-grey-500">Welcome Page</a></li>
      <li><a href="/pages/aboutus.php" class="text-primaryTextColor hover:hover:bg-grey-500">About Us</a></li>
      <li><a href="/pages/products.php" class="text-primaryTextColor hover:hover:bg-grey-500">Products</a></li>
      <?php if(isset($userIsLoggedIn)){ ?>
      <li class="z-50"> 
        <details>
          <summary class="text-primaryTextColor hover:hover:bg-grey-500" >Account</summary>
          <ul class="p-2 w-max">
            <?php if(isset($userRole) && $userRole == 'Admin'){ ?>
          <li>
          <details open>
            <summary class="text-btnPrimary hover:hover:bg-grey-500" >Admin Pages</summary>
            <ul>
            <li class="text-primaryTextColor hover:hover:bg-grey-500"><a href="/pages/admin/listUsers.php">Users Management</a></li>
            <li><a class="text-primaryTextColor" href="/pages/admin/productsmodificationpage.php">Edit Products</a></li>
            </ul>
          </details>
        </li>
        <?php }?>
            <li class="text-primaryTextColor hover:hover:bg-grey-500"><a href="/pages/orderDetails.php">Order Details</a></li>
          

          </ul>
        </details>
      </li>
      <?php } ?>
      <li> <a href="/pages/myCart.php" class="text-primaryTextColor hover:hover:bg-grey-500">Cart<?php ?></a>

        <!--
      <div class="dropdown ">
      <div tabindex="0" role="button" class="btn  btn-circle">
        <div class="indicator">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="badge badge-sm indicator-item">8</span>
        </div>
      </div>
      <div
        tabindex="0"
        class="card card-compact dropdown-content bg-base-100 z-[1] mt-13 w-52 shadow">
        <div class="card-body">
          <span class="text-lg font-bold">8 Items</span>
          <span class="text-info">Subtotal: $999</span>
          <div class="card-actions">
            <button class="btn btn-primary btn-block">View cart</button>
          </div>
        </div>
      </div>
    </div>
-->
        </li>
    </ul>
  </div>
  <div class="navbar-end">
    <!--
  <label class="swap swap-rotate mr-2">
    <input type="checkbox" id="toggleDarkMode" onclick="toggleDarkMode()" />

    -- Sun icon  --
    <svg class="swap-on fill-current w-10 h-10 text-primaryTextColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path
        d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
    </svg>

    -- Moon icon --
    <svg class="swap-off fill-current w-10 h-10 text-primaryTextColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path
        d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
    </svg>
  </label>
-->
    <a class="btn bg-btnPrimary text-white" href="<?php echo ($user == null) ? '/pages/auth/login.php' : '/pages/auth/register.php'; ?>" >
      <?php echo ($user == null) ? 'Login' : 'Logout'; ?>
    </a>
  </div>
</div>