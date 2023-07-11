<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.ico?" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <link rel="stylesheet" type="text/css" href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" />
    <script src="./scripts/jquery-3.7.0.min.js"></script>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./scripts/popper.min.js"></script>
    <script src="./scripts/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        $(document).ready(function() {

            $('.nav-link').click(function() {
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">

                <img src="./images/git.png" alt="Logo" width="34" class="d-inline-block align-text-top" />
                GIT Employees Cooperative Credit Scoeity

            </a>

        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="#">HOME</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" href="about.php">ABOUT US</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        GOVERNANCE
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../../governance.php?id=patrons">Patrons</a></li>
                        <li><a class="dropdown-item" href="../../governance.php?id=directors">Directors</a></li>
                        <li><a class="dropdown-item" href="../../governance.php?id=auditors">Auditors</a></li>
                        <li><a class="dropdown-item" href="../../governance.php?id=legaladvisor">Legal Advisor</a></li>
                        <li><a class="dropdown-item" href="../../governance.php?id=officestaff">Office Staff</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        FACILITIES
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="newmembership.php">New Membership</a></li>
                        <li><a class="dropdown-item" href="loans.php">Loans</a></li>
                        <li><a class="dropdown-item" href="updateshare.php">Update Share Contribution</a></li>
                        <li><a class="dropdown-item" href="updateemi.php">Update EMI</a></li>
                        <li><a class="dropdown-item" href="closemembership.php">Membership Closure</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="activities.php">ACTIVITES</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mandatory.php">MANDATORY DISCLOSURE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">LOGIN</a>
                </li>
            </ul>

        </div>
    </nav>