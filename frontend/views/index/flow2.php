<?php
/*foreach ($carts as $cart){
    var_dump($cart->goods->goodsGallery[0]->path);exit();
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>填写核对订单信息</title>
	<link rel="stylesheet" href="/style/base.css" type="text/css">
	<link rel="stylesheet" href="/style/global.css" type="text/css">
	<link rel="stylesheet" href="/style/header.css" type="text/css">
	<link rel="stylesheet" href="/style/fillin.css" type="text/css">
	<link rel="stylesheet" href="/style/footer.css" type="text/css">

	<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="/js/cart2.js"></script>

</head>
<body>
	<!-- 顶部导航 start -->
	<div class="topnav">
		<div class="topnav_bd w990 bc">
			<div class="topnav_left">
				
			</div>
			<div class="topnav_right fr">
				<ul>
					<li>您好，欢迎来到京西！[<a href="login.html">登录</a>] [<a href="register.html">免费注册</a>] </li>
					<li class="line">|</li>
					<li>我的订单</li>
					<li class="line">|</li>
					<li>客户服务</li>

				</ul>
			</div>
		</div>
	</div>
	<!-- 顶部导航 end -->
	
	<div style="clear:both;"></div>
	
	<!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="index.html"><img src="/images/logo.png" alt="京西商城"></a></h2>
			<div class="flow fr flow2">
				<ul>
					<li>1.我的购物车</li>
					<li class="cur">2.填写核对订单信息</li>
					<li>3.成功提交订单</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 页面头部 end -->
	
	<div style="clear:both;"></div>

    <form action="#" method="post">

    <div class="fillin w990 bc mt15">
            <div class="fillin_hd">
                <h2>填写并核对订单信息</h2>
            </div>

            <div class="fillin_bd">
                <!-- 收货人信息  start-->
                <div class="address">
                    <h3>收货人信息</h3>
                    <div class="address_info">
                        <p>
							<?php foreach ($address as $addr):?>
                            <input type="radio" <?=$addr->is_default==1?'checked="checked"':''?> value="<?=$addr->id?>" name="addr_id"/><?=$addr->consignee.'&emsp;'.$addr->tel.'&nbsp;'.$addr->prov.'&nbsp;'.$addr->city.'&nbsp;'.$addr->area.'&nbsp;'.$addr->de_address?></p>
						<?php endforeach;?>
                    </div>
                </div>
                <!-- 收货人信息  end-->

                <!-- 配送方式 start -->
                <div class="delivery">
                    <h3>送货方式 </h3>


                    <div class="delivery_select">
                        <table>
                            <thead>
                            <tr>
                                <th class="col1">送货方式</th>
                                <th class="col2">运费</th>
                                <th class="col3">运费标准</th>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ($shippingMethod as $item=>$shipping):?>

                                <tr <?=$item==1?'class="cur"':''?> data-price="<?=$shipping[1]?>.00">
                                    <td><input type="radio" <?=$item==1?'checked="checked"':''?> name="delivery_id" value="<?=$item?>" /><?=$shipping[0]?></td>
                                    <td>￥<?=$shipping[1]?>.00</td>
                                    <td><?=$shipping[2]?></td>
                                </tr>
							<?php endforeach;?>
                            </tbody>
                        </table>

                    </div>
                </div>
                <!-- 配送方式 end -->

                <!-- 支付方式  start-->
                <div class="pay">
                    <h3>支付方式 </h3>


                    <div class="pay_select">
                        <table>
							<?php foreach ($paymentMethod as $item=>$payment):?>
                                <tr <?=$item==1?'class="cur"':''?>>
                                    <td  class="col1"><input <?=$item==1?'checked="checked"':''?> type="radio" name="payment_id" value="<?=$item?>" /><?=$payment[0]?></td>
                                    <td class="col2"><?=$payment[1]?></td>
                                </tr>
							<?php endforeach;?>
                        </table>

                    </div>
                </div>
                <!-- 支付方式  end-->

                <!-- 商品清单 start -->
                <div class="goods">
                    <h3>商品清单</h3>
                    <table>
                        <thead>
                        <tr>
                            <th class="col1">商品</th>
                            <th class="col3">价格</th>
                            <th class="col4">数量</th>
                            <th class="col5">小计</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
						//计算商品数量
						$price_totals = 0;
						$num_totals = 0;
						foreach ($carts as $cart):
							//这个商品的总价
							$price_total = $cart->goods->shop_price*$cart->amount;//价格
							$price_totals += $price_total;//总价格
							$num_total = $cart->amount;//数量
							$num_totals += $num_total//总数量
							?>

                            <tr>
                                <td class="col1"><a href=""><img src="<?=$cart->goods->goodsGallery[0]->path?>" alt="" /></a> <strong><a href=""><?=$cart->goods->name?></a></strong></td>
                                <td class="col3">￥<?=$cart->goods->shop_price?></td>
                                <td class="col4"><?=$num_total?></td>
                                <td class="col5"><span>￥<?=$price_total?></span></td>
                            </tr>
							<?php

						endforeach;
						?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5">
                                <ul>
                                    <li>
                                        <span><?=$num_totals?> 件商品，总商品金额：</span>
                                        <em>￥<?=$price_totals?>.00</em>
                                    </li>
                                    <li data-fx="fx">
                                        <span>返现：</span>
                                        <em>-￥0.00</em>
                                    </li>
                                    <li data-yf="yf">
                                        <span>运费：</span>
                                        <em>￥10.00</em>
                                    </li>
                                    <li data-total="total">
                                        <span>应付总额：</span>
                                        <em>￥<?=$price_totals?>.00</em>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- 商品清单 end -->

            </div>

            <div class="fillin_ft">

                <span><input type="submit" value="提交订单"></span>
                <p>应付总额：<strong data-totals="zj">￥5076.00元</strong></p>

            </div>
        </div>
    </form>
        <!-- 主体部分 end -->

        <div style="clear:both;"></div>
        <!-- 底部版权 start -->
        <div class="footer w1210 bc mt15">
            <p class="links">
                <a href="">关于我们</a> |
                <a href="">联系我们</a> |
                <a href="">人才招聘</a> |
                <a href="">商家入驻</a> |
                <a href="">千寻网</a> |
                <a href="">奢侈品网</a> |
                <a href="">广告服务</a> |
                <a href="">移动终端</a> |
                <a href="">友情链接</a> |
                <a href="">销售联盟</a> |
                <a href="">京西论坛</a>
            </p>
            <p class="copyright">
                © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
            </p>
            <p class="auth">
                <a href=""><img src="/images/xin.png" alt="" /></a>
                <a href=""><img src="/images/kexin.jpg" alt="" /></a>
                <a href=""><img src="/images/police.jpg" alt="" /></a>
                <a href=""><img src="/images/beian.gif" alt="" /></a>
            </p>
        </div>
        <!-- 底部版权 end -->
<script type="text/javascript">
    $().ready(function () {
        var datap = $("input[checked]").closest("tr").attr('data-price');
        $("li[data-yf='yf'] em").text("￥"+datap);
        $("strong[data-totals='zj']").text("￥"+(<?=$price_totals?>+(datap-0))+".00元");
        console.debug(<?=$price_totals?>+(datap-0));
    });
    $("input[name='delivery_id']").on('click',function () {
        var datap = $(this).closest("tr").attr('data-price');
        $("li[data-yf='yf'] em").text("￥"+datap);
        $("strong[data-totals='zj']").text("￥"+(<?=$price_totals?>+(datap-0))+".00元");
    })    
</script>
</body>
<!-- 主体部分 start -->
</html>
