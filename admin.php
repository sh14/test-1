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

//добавление билета
function add_ticket( $time ) {
	if ( isset ($_POST['time']) && mb_strlen(($_POST['time']), 'UTF-8') > 4 ) { 
		if ( $link = mysqli_connect("localhost", "root", "", "kontrol") ) {
			mysqli_set_charset($link, "utf8"); 
			
			$time = $_POST['time'];
			
			$searchsql = " SELECT * FROM tickets WHERE time = '$time' ";
			$result = mysqli_query($link, $searchsql );
			
			if ( ! ($row = mysqli_fetch_assoc ($result) ) ) {//если не нашел в БД
			$insertsql = " INSERT INTO tickets ( time ) VALUES ('$time') ";
				if ( mysqli_query($link, $insertsql ) ) {
					echo $time . " - Время успешно добавлено";
				}
			}
			else {
				echo $time . "Такое время уже есть";
			}
			mysqli_close ( $link );
		}
	}
	else {
		echo "Заполните билет в формате 00:00";
	}
}

//проверка существования email
function check_user( $check_email ) { 
	if( isset( $_POST['check_email'] ) && !empty( $_POST['check_email'] ) ) {
		if ( $link = mysqli_connect("localhost", "root", "", "kontrol") ) {
		mysqli_set_charset($link, "utf8");
		
		$user_id = 0; //возвращает 0 при отсутствии в БД
		$check_email = filter_var( $_POST['check_email'], FILTER_VALIDATE_EMAIL );
		
			if( $check_email ) {
				$search_email_sql = "SELECT * FROM users WHERE email = '$check_email' ";
				$resul_sql = mysqli_query($link, $search_email_sql );
				if ( $row = mysqli_fetch_assoc ($resul_sql) ) {
					$user_id = $row['id'];
					mysqli_close ( $link );
				}
			}
		}
	}
	return $user_id;//возвращаем id пользователя
}

//проверка билетов пользователя
function check_user_tickets( $check_email ) {
	if( isset( $_POST['check_email'] ) && !empty( $_POST['check_email'] ) ) {
		if ( $link = mysqli_connect("localhost", "root", "", "kontrol") ) {
		mysqli_set_charset($link, "utf8");
		
			$user_id = check_user( $check_email );//проверка на существование 1-сущ, 0-нет
			
			if( $user_id == 0 ) {
				echo 'Такой Email не найден';
			}
			
			if( $link = mysqli_connect( "localhost","root","","kontrol" ) ) {
				mysqli_set_charset( $link, "UTF-8" );
				
				$siteuser_email = filter_var( $_POST['check_email'], FILTER_VALIDATE_EMAIL );
				
				if ( $siteuser_email ) {
					$selest = "SELECT * FROM tickets WHERE user_id = '$user_id'";
					$result = mysqli_query($link, $selest);
					
					echo 'Email брони - <strong>' . $siteuser_email . '</strong>:' . '<br>';
					while( $all_tickets = mysqli_fetch_assoc( $result ) ) { // цикл для поиска всех постов
						echo 'Забронированное время: <strong>' . $all_tickets['time'] . '</strong><br>';
					}
					mysqli_close ($link);
				}
			}
		}
	}
}

//проверить доступные билеты
function check_time( $check_time ) {
	if( isset( $_POST['check_time'] ) && !empty( $_POST['check_time'] ) ) {
		if ( $link = mysqli_connect("localhost", "root", "", "kontrol") ) {
			mysqli_set_charset($link, "utf8"); 
			
			$check_time = $_POST['check_time'];
			
			$search = " SELECT * FROM tickets WHERE time = '$check_time' ";
			$result = mysqli_query($link, $search );
			
			if ( ($row = mysqli_fetch_assoc ($result) ) ) {
				if( $row['token'] == 1 ) {
					echo $check_time . ' - данное время забронировано';
					mysqli_close ( $link );
				}
				else {
					echo $check_time . ' - данное время свободно для бронирования';
				}
			}
			else {
				echo $check_time . ' - Билетов на данное время нет';
			}
		}
	}
}

//получаем email адреса
function get_emails() {
	if ( $link = mysqli_connect("localhost", "root", "", "kontrol" ) ) {
		mysqli_set_charset($link, "utf8"); 
		
		$emails_array = array();
		
		$query_emails = "SELECT email FROM users";
		$result_emails = mysqli_query( $link, $query_emails );
		
		while( $row_emails = mysqli_fetch_assoc ($result_emails) ) {
				$emails_array[] = $row_emails['email'];
		}
		mysqli_close ( $link );
		return $emails_array;
	}
}

//рассылка на email адреса
function send_emails( $subject, $content ) {
	$emails_array = get_emails();
	
	foreach( $emails_array as $value ) { 
		// mail( $row['mail'], $subject, $row['name'], . ',\n' . $content ); //функция отправки почты
		if( ! mail( $value, $subject, $content ) ) {
			return false; //если почта не была отправлена, сразу выдаем ошибку
		}
	}
	return true; // если все отработало нормально
}

//отправка на email адреса
function send_emails_to() {
	if ( isset($_POST['mailing']) && !empty($_POST['mailing'] ) ) {
		if ( send_emails ( $_POST['mailing'], $_POST['mailing'] ) ) {
			echo "Рассылка успешно осуществлена";
		}
	}
}
	
?>

<style>
	form {
		display: none;
	}
	.block {
		margin: 10px;
		padding: 10px;
		border-bottom: 1px solid grey;
	}
</style>
<h1 class="text-center">Выберите действие</h1>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12">
		<button class="btn btn-success" data-element="add-ticket">Добавить билет</button>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 block">
		<form id="add-ticket" action="<?=$_SERVER['REQUEST_URI'] ?>"method="POST">
			<div class="form-group">
				<label>Укажите время
				<input id="time" type="text" class="form-control" name="time">
					<script type="text/javascript">
					   jQuery(function($){
					   $("#time").mask("99:99");
					   });
					</script>
				</label>
			</div>
			<button type="submit" name="add_ticket_submit" class="btn btn-default">Добавить</button>
		</form>
		<?= add_ticket( $time );?><br>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-12">
		<button class="btn btn-info" data-element="check-email">Проверить записи пользователя</button>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 block">
		<form id="check-email" action="<?=$_SERVER['REQUEST_URI'] ?>" method="POST">
				<div class="form-group">
				<label><strong>Ваш e-mail: </strong>
				<input type="email" class="form-control" name="check_email"></label>
			</div>
			<button type="submit" name="check_email_submit" class="btn btn-default">Проверить</button>
		</form>
		<?= check_user_tickets( $check_email );?>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-12">
		<button class="btn btn-warning" data-element="check-time" >Проверить доступные билеты</button>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 block">
		<form id="check-time" action="<?=$_SERVER['REQUEST_URI'] ?>" method="POST">
			<div class="form-group">
			<label>Укажите Время
				<input id="check_time" type="text" class="form-control" name="check_time">
					<script type="text/javascript">
					   jQuery(function($){
					   $("#check_time").mask("99:99");
					   });
					</script>
				</label>
			</div>
			<button type="submit" name="check_time_submit" class="btn btn-default">Проверить</button>
		</form>
		<?= check_time( $check_time );?>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-12">
		<button class="btn btn-danger" data-element="mailing" >Рассылка пользователям</button>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-12 block">
		<form id="mailing" action="<?=$_SERVER['REQUEST_URI'] ?>" method="POST">
			<h3>Рассылка</h3>
			<div class="form-group">
				<textarea class="form-control" name="mailing" rows="6" ></textarea>
			</div>
			<button type="submit" name="mailing_submit" class="btn btn-default">Отправить</button>
		</form>
			<?= send_emails_to();?>
	</div>
	
</div>

<script>
jQuery('button').bind('click', function () {
	jQuery('form').hide();
	var attr = jQuery(this).attr('data-element');
	jQuery('#'+attr).show();
});

</script>
<?php require_once 'footer.php'; //подключаем футер?>
test!
