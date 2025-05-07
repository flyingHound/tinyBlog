<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            width: 100vw;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            position: relative;
            text-align: center;
        }

        .not-found {
            position: relative;
            z-index: 2;
        }

        .not-found h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .not-found a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .not-found a:hover {
            text-decoration: underline;
        }

        .not-found div:last-child {
            position: absolute;
            font-size: 15vw;
            color: rgba(150, 150, 150, 0.2);
            font-weight: bold;
            z-index: 1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            user-select: none;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="not-found">
        <div><h2>You broke the Page</h2></div>
        <div>return to <a href="<?= BASE_URL ?>blog">the blog</a></div>
        <div>404</div>
    </div>
</body>
</html>