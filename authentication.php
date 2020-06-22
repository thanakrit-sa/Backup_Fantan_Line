<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
<script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
  <script>
    function runApp() {
      liff.getProfile().then(profile => {
        window.location.href = "profile.php?userID=" + profile.userId;
      }).catch(err => console.error(err));
    }
    liff.init({ liffId: "1654375936-D1bXp1Xg" }, () => {
      if (liff.isLoggedIn()) {
        runApp()
      } else {
        liff.login();
      }
    }, err => console.error(err.code, error.message));
  </script>