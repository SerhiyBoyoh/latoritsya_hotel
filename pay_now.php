<?php

require_once('admin/inc/db_config.php');
// Перевірка, чи сесія не активна, перед викликом session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    // Перенаправлення користувача на головну сторінку, якщо він не увійшов у систему
    header("Location: index.php");
    exit;
}

if(isset($_POST['pay_now']))
{
    $ORDER_ID = 'ORD_'.$_SESSION['uId'].random_int(11111,9999999);
    $CUST_ID = $_SESSION['uId'];
    $TXN_AMOUNT = $_SESSION['room']['payment'];

    // Insert booking data into database
    $frm_data = filteration($_POST);

    $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `order_id`, `booking_status`, `trans_status`, `trans_amt`) VALUES (?,?,?,?,?,?,?,?)";
    insert($query1, [$CUST_ID, $_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout'], $ORDER_ID, 'booked', 'TXN_SUCCESS', $TXN_AMOUNT], 'isssssss');

    $booking_id = mysqli_insert_id($con);

    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
    insert($query2, [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']], 'issssss');

    // Set success flag
    $success = true;
}

// Update room availability
if (isset($success) && $success) {
    $query_update_room = "UPDATE `rooms` SET `status` = 0 WHERE `id` = ?";
    update($query_update_room, [$_SESSION['room']['id']], 'i');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <title><?php echo $settings_r['site_title'] ?> - Успішне бронювання</title>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

<body class="bg-light">
<style>
    .success-icon {
        font-size: 3rem;
        color: green;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-12 my-5 mb-4 px-4 text-center">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <h2 class="fw-bold mt-3">Успішне бронювання</h2>
            <p>Ваш номер було успішно заброньовано. Дякуємо за ваше замовлення!</p>
            <!-- You can add more details or links here -->
        </div>
    </div>
</div>

</body>

<?php require('inc/footer.php'); ?>
</body>
</html>
