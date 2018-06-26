</div>
<div id="book_Modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Заголовок модального окна -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Бронирование билета</h4>
			</div>
			<!-- Основное содержимое модального окна -->
			<div class="modal-body">
				<form action="<?=$_SERVER['REQUEST_URI'] ?>" method="post">
					<div class="form-group">
						<label for="order-name"><strong>Ваше имя: </strong></label>
						<input type="text" name="siteuser_name" class="form-control siteuser_name" id="siteuser_name">
					</div>
					<div class="form-group">
						<label for="order-phone2"><strong>Ваш e-mail: </strong></label>
						<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" name="siteuser_email">
						<small id="phoneHelp" class="form-text text-muted">Мы никогда не передаем Ваши данные третьим
							лицам
						</small>
					</div>
					<input type="hidden" id="taken_id" name="taken_id">
					<button type="submit" class="btn btn-success" name="take_ticket">Отправить</button>
				</form>
			</div>
			<!-- Футер модального окна -->
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>
<script>
	var url_arr = [];
	url_arr = location.href.split('/');
	if ( ! url_arr[url_arr.length-1] ) {
		url_arr[url_arr.length-1] = 'index.php';
	}
	jQuery('.nav a').removeClass('active');
	url_arr.forEach(function(item, i, url_arr) {
		jQuery('.nav a').each(function () {
			if ( jQuery(this).attr('href') == item ) {
				jQuery(this).parent().addClass('active');
			}
			
		});
	});
	jQuery('body').on('click', '[data-element="to_book"]', function () {
		var selectedElem = jQuery('#ticket_time').val();
		jQuery('#taken_id').val( selectedElem );
		jQuery('#book_Modal').modal('show');
		return false;
	});
</script>
</body>
</html>