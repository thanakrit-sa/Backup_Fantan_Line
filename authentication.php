<script>
    function runApp() {
        liff.getProfile().then(profile => {
            window.location.href = "profile..php?userID=" + profile.userId;
        }).catch(err => console.error(err));
    }
    liff.init({
        liffId: "1654375936-D1bXp1Xg"
    }, () => {
        if (liff.isLoggedIn()) {
            runApp()
        } else {
            liff.login();
        }
    }, err => console.error(err.code, error.message));
</script>