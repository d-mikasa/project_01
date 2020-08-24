function Delete_check() {
    var res = confirm("削除してもよろしいですか？");
    if (res == true) {
            // OKなら移動

        }
        else {
            // キャンセルならアラートボックスを表示
            window.location.href = 'room_list.php'
        }
    }
