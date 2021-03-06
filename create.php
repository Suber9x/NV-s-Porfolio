
<?php require_once 'core/init.php'; ?>

<?php 
$err = array();
if(Input::exists()) {
    if(Token::check(Input::get('token'))){
        $validation = new Validation();
        $validate = $validation->check($_POST, array(
            'service_name' => array(
                'name' => 'Tên Dịch Vụ',
                'required' => true,
                'min' => 2,
                'max' => 20
            ),
            'service_username' => array(
                'name' => 'Tên Đăng Nhập',
                'required' => true,
                'min' => 6,
                'max' => 20
            ),
            'service_password' => array(
                'name' => 'Mật Khẩu Dịch Vụ',
                'required' => true,
                'min' => 8,
                'max' => 20
            ),
        ));

        if($validate->passed()){
            $account = new Account();
            $date = new DateTime();
            $date = $date->format('d-m-Y H:i:s');
            $addition = Input::get('addition');
            $additionVal = Input::get('additionVal');
            $obj = array();
            for($i=0; $i<sizeof($addition); $i++)
                for($j=0; $j<sizeof($additionVal); $j++){
                    $obj[$addition[$i]] = $additionVal[$j]; 
                }
            $obj = json_encode($obj);

            try{
                $account->create(array(
                    'user_id' => Session::get(Config::get('session/session_name')),
                    'name' => Input::get('service_name'),
                    'username' => Input::get('service_username'),
                    'password' => Input::get('service_password'),
                    'date' => $date,
                    'addition_info' => $obj,
                ));
                Session::flash('success', 'Một Tài Khoản Vừa Được Tạo');
                Redirect::to('index.php');

            }catch(Exception $e) {
                    die($e->getMessage());
                } 
        }
        else {
            foreach ($validate->errors() as $key => $value) {
                    $err[] = $value;
                }
        }
    }
   
}


?>


<?php View::include('blocks/header.php') ?>

    <div class="error-display" style="display: fixed; left:0; bottom: 0; position: fixed">
        <?php 
            if(!empty($err)){
                foreach ($err as $key => $value) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <strong>Lỗi!</strong> '.$value.'</div>';
                }
            }
        ?>
    </div>

	<div class="jumbotron">
      <div class="container">
        <h1 class="display-3">THÔNG TIN TÀI KHOẢN MỚI</h1>
        <p>Đây là lưu trữ thông tin tài khoản mới của bạn.</p>
        <p><a class="btn btn-warning btn-lg" href="index.php" role="button">Trở Về &raquo;</a></p>
      </div>
    </div>

    <div class="container">
    	<form action="" method="post" class="form-control" id="form-box">
    		<div id="appendbox">
                <div>
                    <label for="service_name">Tên Dịch Vụ</label>
                    <input type="text" class="form-control" name="service_name">
                </div>
                <div>
                    <label for="service_username">Username/Email</label>
                    <input type="text" class="form-control" name="service_username">
                </div>
                <div>
                    <label for="service_password">Mật Khẩu</label>
                    <input type="text" class="form-control" name="service_password">
                </div>
                <br>
                <div class="float-right" id="genPasswrapper">
                    <div class="btn btn-success" onclick="makePassword('action');" style="color:#fff; cursor: pointer;" class="form-control">Sinh mật khẩu? (+)</div>
                    <div id="gen"></div>
                </div>
                <div class="float-left">
                    <div href="#" class="btn btn-primary" onclick="loadMore();" style="color:#fff; cursor: pointer;" class="form-control">Thêm trường thông tin? (+)</div>
                </div>
                <div class="clearfix"></div>
                <br>

                <div class="clearfix"></div>      
            </div>

            <br>
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
            <input type="submit" name="" class="form-control btn btn-warning" value="Thêm">
    	</form>

    <script type="text/javascript">

        //Make Password
        function makePassword(){

            var req = new XMLHttpRequest();
            var url = 'genpass.php';
            var params = "genpassword";

            req.open("POST", url, true);
            req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            req.send("action=" + params);
            req.onreadystatechange = function() {
                if(req.readyState == 400 || req.status == 200) {
                    var passbox = '';
                    passbox += '<div id="copyTxt" style="border: 1px solid rgba(0,0,0,.15); height: 30px; border-radius: 5px; box-sizing: border-box; padding: 3px; display: flex"><span class="float-left">';
                    passbox += req.responseText;
                    passbox += '</span> <i onclick="copyText();" style="cursor: pointer;" class="fa fa-clipboard" aria-hidden="true"></i></div>';

                    $('#gen').html(passbox);
                }
            }
            
        }

        //Load Addtional Info Form
        function loadMore(){
            input = '';
            input = '<div style="display: flex;"><div class="col-md-6"><label for="pincode">Tên Trường Thông Tin</label><input type="text" class="form-control" name="addition[]" placeholder="vd: Câu hỏi bí mật..."></div><div class="col-md-6"><label for="pincode">Giá Trị</label><textarea class="form-control" name="additionVal[]" placeholder="vd: Con mèo..."></textarea></div> </div><div class="clearfix"></div>';

            $('#appendbox').append(input);
        }


        function copyText(){
            var password = $('#copyTxt').text();
            var temp = $('<input>');
            $('body').append(temp);
            temp.val(password).select();
            document.execCommand('copy');
            temp.remove();
            alert("Copied");
        }


    </script>



<?php View::include('blocks/footer.php') ?>

