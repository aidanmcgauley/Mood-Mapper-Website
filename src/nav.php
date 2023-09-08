<link rel="stylesheet" href="../css/style.css">

<nav class="navbar navbar-expand-md navbar-light border">
        <div class="container"> <!-- 100% width at all screen sizes -->
            <a href="index.php" class="navbar-brand">
                <img class="logo" src="../img/moodmapperlogo.png" width="300" height="75">
            </a>
            
            <button 
                class="navbar-toggler" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navMenu">

                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse menuR" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    
                <?php if (!isset($data) || !isset($_SESSION["user_id"])): ?>
                    <li class="nav-item">
                        <a href="login-signup.php" class="nav-link">Login or Sign Up</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <p class="navbar greet">Hello, <?= htmlspecialchars($first_name) ?>!</p>
                    </li>
                    <li class="nav-item">
                    <a href="index.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="moodlogging.php" class="nav-link">Mood Logging</a>
                    </li>
                    <li class="nav-item">
                        <a href="moodlist.php" class="nav-link">Mood List</a>
                    </li>
                    <li class="nav-item">
                        <a href="chart1.php" class="nav-link">Mood Summary</a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Account</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php" onclick="logout()">Log out</a></li>
                            <li><a class="dropdown-item" href="delete-account.php">Delete Account</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                </ul>
            </div>
                
        </div>
    </nav>