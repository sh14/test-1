<?php 
	require_once 'header.php';

/* 
mysqli_fetch_assoc - переводит объект в массив
$link = mysqli_connect("localhost", "my_user", "my_password", "world");  - открываем соединение с БД
mysqli_query - Выполняет запрос
mysqli_close ($link); - закрываем соединение
INSERT INTO posts (title, content) VALUES ('Первая запись', 'Тестовый контент1'); - Вставляем запись в БД
UPDATE posts SET content = 'Новый контент' WHERE id = '1'; - обновляем запись в БД
DELETE FROM posts WHERE id = 1; - удаляем запись из БД
SELECT * FROM posts WHERE id=1; - получаем записи из БД
SELECT * FROM posts WHERE `id` IN (1, 2, 3) and (1, 2, 3)
*/

//поиск и вывод свободных билетов для юзера
function empty_tickets() { 
	if ( $link = mysqli_connect("localhost", "root", "", "kontrol") ) {
		mysqli_set_charset($link, "utf8");
		
		$allpost = "SELECT * FROM tickets WHERE token = 0 ";
		$resultall = mysqli_query($link, $allpost );
		$empty_tickets = array();
		
		while ($row2 = mysqli_fetch_assoc ($resultall) ) {
			echo '<option value="' . $row2['id'] . '">' . $row2['time'] . '</option>';
		}
		mysqli_close ( $link );
	}
}

//функция проверки пользователя по email
function check_user( $siteuser_email ) {
	if( isset( $_POST['siteuser_email'] ) && !empty( $_POST['siteuser_email'] ) ) {
		if ( $link = mysqli_connect("localhost", "root", "", "kontrol") ) {
		mysqli_set_charset($link, "utf8");
		
		$user_id = 0; //возвращает 0 при отсутствии в БД
		$siteuser_email = filter_var( $_POST['siteuser_email'], FILTER_VALIDATE_EMAIL );
		
			if( $siteuser_email ) {
				$search_email_sql = "SELECT * FROM users WHERE email = '$siteuser_email' ";
				$resul_sql = mysqli_query($link, $search_email_sql );
				if ( $row = mysqli_fetch_assoc ($resul_sql) ) {
					$user_id = $row['id'];
					//echo 'всё работает, вот id пользователя из БД = ' . $user_id;
				mysqli_close ( $link );
				}
			}
		}
	}
	return $user_id;//возвращаем id пользователя
}

//функция "добавление пользователя"
function add_user( $siteuser_email, $siteuser_name ) {
	if ( isset ( $_POST['siteuser_name'] ) && !empty( $_POST['siteuser_name'] ) && 
	isset ( $_POST['siteuser_email'] ) && !empty( $_POST['siteuser_email'] ) ) {
		if($link = mysqli_connect("localhost","root","","kontrol")) {
			mysqli_set_charset( $link, "UTF-8" );
			
			$user_id = check_user( $siteuser_email ); //проверка на существование
			
			if( $user_id == 0 ) {
				$siteuser_email = filter_var( $_POST['siteuser_email'], FILTER_VALIDATE_EMAIL );
				$siteuser_name=$_POST['siteuser_name'];
				
				if ( $siteuser_email ) {
					$insertsql = "INSERT INTO users (name, email) VALUES('$siteuser_name','$siteuser_email')";
					$result = mysqli_query($link, $insertsql);
					$id = mysqli_insert_id ( $link ); //присваиваем id переменной
					echo 'Новый пользователь. Email: <strong>' . $siteuser_name . '</strong>; Имя: <strong>' . $siteuser_email . '</strong>; возврат функции - id пользователя=';
				mysqli_close ( $link );
				}
			}
		}
	}
	return $id;//возврат рез-та
}

//функция "бронирование билетов"
function to_book( $siteuser_email, $siteuser_name , $time_id ) {
	if ( isset ( $_POST['siteuser_name'] ) && !empty( $_POST['siteuser_name'] ) && 
	isset ( $_POST['siteuser_email'] ) && !empty( $_POST['siteuser_email'] ) ) {
		
		$user_id = check_user( $siteuser_email ); //проверка на существование, если сущ, то получаем id
		
		if( $user_id == 0 ) {
			add_user( $siteuser_email, $siteuser_name );
		}
		
		else if( $link = mysqli_connect( "localhost","root","","kontrol" ) ) {
			mysqli_set_charset( $link, "UTF-8" );
			
			$siteuser_email = filter_var( $_POST['siteuser_email'], FILTER_VALIDATE_EMAIL );
			$time_id = $_POST['taken_id'];
			
			if ( $siteuser_email ) {
				$update = "UPDATE tickets SET token='1', user_id='$user_id' WHERE id = '$time_id'";
				$result = mysqli_query($link, $update);
				echo 'Email <strong>' . $siteuser_email . '</strong>; id: <strong>' . $time_id . '</strong>; возврат функции - id билета=';
				mysqli_close ( $link );
			}
		}
	}
	return $time_id;
}

?>

<style>
	.block2 {
		margin: 10px;
		padding: 10px;
		border-bottom: 1px solid grey;
	}
</style>

<h3 class="text-center">Забронировать место</h3>
<form action="<?=$_SERVER['REQUEST_URI'];?>" class="form-horizontal" method="POST">
	<div class="form-group">
		<label class="col-sm-2 control-label"> Выберите время: </label>
		<div class="col-sm-2">
			<select class="form-control" id="ticket_time" name="ticket_time">
			<!-- <option value="<id>"><12:00></option> -->
			<?= empty_tickets()?>
			</select>
		</div>
		<div class="col-sm-3">
			<button type="submit" data-element="to_book" class="btn btn-success">Забронировать</button>
		</div>
	</div>
</form>

<div class="block2">
	<?= add_user( $siteuser_email, $siteuser_name )?>
</div>
<div class="block2">
	<?= to_book( $siteuser_email, $siteuser_name , $user_id )?>
</div>



<?php	require_once 'footer.php';?>