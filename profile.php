<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
</head>

<body style="background-image: url(https://img.freepik.com/free-vector/retro-styled-pattern-background_1048-6593.jpg?size=338&ext=jpg);">
    <?
        $userID = $_GET['userID'];
        $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/profile/' . $userID);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
        $result = curl_exec($ch);
        curl_close($ch);
        $resultData = json_decode($result, true);
        $data = $resultData['data'];
        $user_displayname = $data['user_displayname'];
        $line_id = $data['user_lineid'];
        $credit = $data['credit'];
    ?>
    <div class="container">
        <br>
        <div align="center">
            <label class="m-0" style="color: white; letter-spacing:3px; font-weight: bold; background-color:black;">
                <h4 class="p-0 m-2">Profile</h4>
            </label>
            <label class="m-0" style="color: white; letter-spacing:3px; font-weight: bold; background-color:black;">
                <h4 class="p-0 m-2">Id, Lineid, Balance</h4>
            </label>
        </div>
        <br>
        <div class="card card-body m-0">
            <div class="form-group m-0 p-0">
                <div class="input-group shadow">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="width:65px"><i class="fas fa-user-alt m-auto" style="font-size:30px"></i></div>
                    </div>
                    <input type="text" class="form-control form-control-lg bg-white" readonly placeholder="<?=$user_displayname?>">
                </div>
            </div>
            <div class="form-group m-0 p-0">
                <div class="input-group mt-3 shadow">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="width:65px"><i class="far fa-id-card m-auto" style="font-size:30px"></i></div>
                    </div>
                    <input type="text" class="form-control form-control-lg bg-white" readonly placeholder="<?=$line_id?>">
                </div>
            </div>
            <div class="form-group m-0 p-0">
                <div class="input-group mt-3 shadow">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="width:65px"><i class="fas fa-money-check-alt m-auto" style="font-size:30px"></i></div>
                    </div>
                    <input type="text" class="form-control form-control-lg bg-white" readonly placeholder="<?=number_format($credit);?> บาท">
                </div>
            </div>
        </div>
    </div>
</body>

</html>