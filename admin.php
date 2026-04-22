<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head><title>Admin - Quety</title></head>
<body style="text-align:center; padding: 50px; font-family: sans-serif;">
    <h1>Meeting Administration</h1>
    <button onclick="resetBoard()" style="padding: 20px; background: red; color: white; border: none; cursor: pointer;">
        CLEAR ALL DATA (RESET)
    </button>
    <br><br>
    <a href="index.php">Back to App</a>

    <script>
        function resetBoard() {
            if(confirm("Are you sure you want to clear the queue and counts?")) {
                fetch('api.php?action=reset').then(() => alert("Board Cleared"));
            }
        }
    </script>
</body>
</html>