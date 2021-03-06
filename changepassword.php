<?php require_once 'core/init.php' ?>

<?php 

	$err = array();
	
	if(Input::exists()){
		if(Token::check(Input::get('token'))) {
			$validation = new Validation();
			$validate = $validation->check($_POST, array(
				'old_password' => array(
	                'name' => 'Mật Khẩu Cũ',
	                'required' => true,
	                'min' => 6,
	            ),
	            'new_password' => array(
	                'name' => 'Mật Khẩu Mới',
	                'required' => true,
	                'min' => 6,
	                'matches' => 'password_confirm',
	            ),
	            'password_confirm' => array(
	                'name' => 'xác nhận mật khẩu',
	                'required' => true,
	                'min' => 6,
	                
	            )
			));

			if($validate->passed()){
				$user = new User();
				$user->find(Session::get(Config::get('session/session_name')));
			
				
				$original_password = $user->data()->password;
				$original_salt = $user->data()->salt;
				$input_current_password = Hash::make(Input::get('old_password'), $original_salt);
				$new_salt = Hash::salt(32);
				$id = Session::get(Config::get('session/session_name'));

				if($original_password == $input_current_password){
					//var_dump($user->changePassword(array('password','=', Hash::make(Input::get('new_password'), $new_salt), ',', 'salt', '=', $new_salt , '/' ,'id', $id)));
					if($user->changePassword(array('`password`','=', "'".Hash::make(Input::get('new_password'), $new_salt)."'", ',', '`salt`', '=', "'".$new_salt."'" , '/' ,'id', $id))){
						Session::flash('success', 'Mật khẩu vừa được cập nhật.');
						Redirect::to('index.php');
					} else {
						$err[] = 'Có lỗi xảy ra trong quá trình cập nhật.';
					}
				} else {
					$err[] = 'Mật Khẩu Cũ Không Đúng.';
				}
				

				// try{
				// 	$user->changePassword(Session::get(Config::get('session/session_name')), array(
				// 		'password','=', Hash::make(Input::get('new_password'), $salt), 
				// 		'salt', '=', $salt,'id', $id));
				// 	echo "Dang chay";

				// 	// Session::flash('success_change', 'Mật Khẩu Vừa Được Thay Đổi. Vui Lòng Đăng Nhập Lại.');
				// 	// Redirect::to('index.php');

				// }catch(Exception $e) {
				// 	die($e->getMessage());
				// }
			} else {
            	foreach ($validation->errors() as $key => $value) {
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
		if(Session::exists('error')) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Lỗi!</strong> '.Session::flash('error').'</div>';
          }
	?>
</div>

<div class="jumbotron">
  <div class="container">
    <h1 class="display-3">THAY ĐỔI MẬT KHẨU</h1>
    <p>Để thay đổi mật khẩu vui lòng điền thông tin thích hợp vào form bên dưới.</p>
    <p><a class="btn btn-success btn-lg" href="create.php" role="button">Đổi Mã Pin &raquo;</a></p>
  </div>
</div>

<div class="container">
	<form class="form-control" action="" method="post">
		<div>
			<label  for="old_password">Mật Khẩu Hiện Tại</label>
			<input type="password" name="old_password" class="form-control">
		</div>
		<div>
			<label  for="new_password">Mật Khẩu Mới</label>
			<input type="password" name="new_password" class="form-control">
		</div>
		<div>
			<label  for="password_confirm">Xác Nhận Mật Khẩu</label>
			<input type="password" name="password_confirm" class="form-control">
		</div>

		<br>
        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" name="" class="form-control btn btn-success" value="Thay Đổi">
	</form>

<?php View::include('blocks/footer.php') ?>