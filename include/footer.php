<footer ng-controller="DataCtrl">
	<div class="container">
		<div class="footer-image">
			<img alt="紳泰蘭柚木傢俱" ng-src="{{footerImage}}" />
		</div>
		<div class="ac-gf-footer-locale" ng-bind="footerText"></div>
		<!--<div>連絡電話：(03)3698715-</div>
		<div>連絡電話：0933-136422(戴小姐)</div>-->
	</div>
</footer> 

<div class="blackFrame hidden">


		<div class="sign-frame">
			<div class="inner">
				<div class="cross"></div>
				<form id="registor">
					<div>
						<label>姓名</label>
						<input type="text" name="name" />
					</div>
					<div>
						<label>電話</label>
						<input type="text" name="phone" />
					</div>
					<div>
						<label>信箱</label>
						<input type="text" name="email" />
					</div>

					<div>
						<label>密碼</label>
						<input type="password" name="userId" />
					</div>

					<div>
						<label>再次確認密碼</label>
						<input type="password" name="confirm" />
					</div>

					<div>
						<label>地址</label>
						<input type="text" name="address" />
					</div>
					<!--<div>
						<label></label>
						<select>

							<option ng-repeat="x in 1 .. 100">{{x}}</option>
							
						</select>
					</div>-->
					<div>
						<div class="registorConfirm btn-success btn">確認</div>
						<input type="hidden" name="registor" value="1" />
					</div>
				</form>
				<form id="login">

					<div>
						<label>手機</label>
						<input type="text" name="registorPhone" />
					</div>

					<div>
						<label>密碼</label>
						<input type="password" name="userId" />
					</div>

					<div>
						<div class="loginBtn btn-success btn">確認</div>
					</div>

					<div class="registor">
						<div class="tip">沒有任何帳號?請點註冊帳號</div>
						<div class="registorBtn btn-danger btn">註冊帳號</div>
					</div>

					<div class="other">
						<div class="tip">facebook快速登入</div>
						<div class="fb btn-primary btn">facebook login</div>
					</div>

				</form>

				<form id="update">
					<div>
						<label>姓名</label>
						<input type="text" name="name" />
					</div>
					<div>
						<label>電話</label>
						<input type="text" name="phone" />
					</div>
					<div>
						<label>信箱</label>
						<input type="text" name="email" />
					</div>
					<div>
						<label>地址</label>
						<input type="text" name="address" />
					</div>
					<div>
						<div class="updateConfirm btn-success btn">確認</div>
						<input type="hidden" name="update" value="" />
					</div>
				</form>
				
				<form id="order">

					<div class="product">
						<label class="productName"></label>
						<input type="hidden" name="addOrder" />
						<input type="hidden" name="userId" />
					</div>

					<div>
						<label>數量</label>
						<input type="text" name="productNumber" />
					</div>

					<div>
						<label>問題</label>
						<textarea name="content"></textarea>
					</div>

					<div>
						<div class="orderBtn btn-success btn">確定</div>

					</div>

				</form>

			</div>
		</div>
	</div>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109149991-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-109149991-1');
</script>
