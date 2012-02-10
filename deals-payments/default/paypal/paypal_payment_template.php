<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Payment Redirect</title>
	<?php wp_head(); ?>
</head>
<body id="payment-process">
	
<?php
if(!is_user_logged_in()) {
    wp_die('Please login first to buy this item.');
}

global $current_user;
get_currentuserinfo();

$buyId = get_query_var('deal_buy_id');
$itemData = get_post($buyId);
$itemName = $itemData->post_title;
$itemPrice = get_post_meta($buyId, '_discount_price',true);
$currency   = get_option('deals_currency');

$paypal_email = get_option('deals_paypal_email');
$option_enable_sandbox = get_option('deals_paypal_sandbox');

$ipn = plugins_url('deals-payments/default/paypal/paypal_ipn.php?item_id='.$buyId.'&user_id='.$current_user->ID,DEALS_PLUGIN_FILE);

$paypal_url = !empty($option_enable_sandbox) 
    ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

//if($option_enable_sandbox) {
//    $paypal_email = $option_sandbox;
//}else{
//    $paypal_email = $option_paypal;
//}

$valid_email = filter_var($paypal_email,FILTER_VALIDATE_EMAIL);

?>

<?php if($option_enable_sandbox) : ?>

    <?php if(empty($paypal_email) && !$valid_email) : ?>
        <article id="post-9">
            <header class="entry-header">
                <h2 class="entry-title">Payment Error!</h1>
            </header>
            <div class="processing-error">
                <span class="deal-description">
                    We are sorry, but some error has been detected. Please try again later
                </span>
            </div>        

        </article>
    <?php else: ?>
        <article id="post-9">
                <header class="entry-header">
                    <h2 class="entry-title">Payment Processing</h1>
                </header>
                <div class="processing">
                    <span class="deal-description">You will be redirected to Official Paypal Website for processing your payment <a href="#" id="paypal-js">click here if you're not redirecting</a></span>
                </div>

                <form id="paypal-form" action="<?php echo $paypal_url; ?>">
                    <input type="hidden" name="business" value="<?php echo $paypal_email; ?>" />
                    <input type="hidden" name="rm" value="2" />
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="currency_code" value="<?php echo $currency; ?>" />
                    <input type="hidden" name="notify_url" value="<?php echo $ipn; ?>" />
                    <input type="hidden" name="return" value="<?php echo get_permalink(get_option('deals_page_thanks_post_id')).'?item_id='.$buyId.'&user_id='.$current_user->ID; ?>" />
                    <input type="hidden" name="cancel_return" value="<?php echo get_permalink(get_option('deals_page_post_id')); ?>" />
                    <input type="hidden" name="item_name" value="<?php echo $itemName; ?>" />
                    <input type="hidden" name="item_number" value="<?php echo $buyId; ?>" />                    
                    <input type="hidden" name="amount" value="<?php echo $itemPrice; ?>" />
                </form>

                <script type="text/javascript">
                    jQuery(document).ready(function() {

                        jQuery('#paypal-js').click(function() {
                           jQuery('#paypal-form').submit();
                           return false;
                        });

                        window.setTimeout(function() {

                            jQuery('#paypal-form').submit();

                        },5000);

                    });
                </script>

            </article>
    <?php endif; ?>

<?php else: ?>

    <?php if(empty($paypal_email) && !$valid_email) : ?>
        <article id="post-9">
            <header class="entry-header">
                <h2 class="entry-title">Payment Error!</h1>
            </header>
            <div class="processing-error">
                <span class="deal-description">
                    We are sorry, but some error has been detected. Please try again later
                </span>
            </div>        

        </article>
    <?php else: ?>
            <article id="post-9">
                <header class="entry-header">
                    <h2 class="entry-title">Payment Processing</h1>
                </header>
                <div class="processing">
                    <span class="deal-description">You will be redirected to Official Paypal Website for processing your payment <a href="#" id="paypal-js">click here if you're not redirecting</a></span>
                </div>

                <form id="paypal-form" action="<?php echo $paypal_url; ?>">
                    <input type="hidden" name="business" value="<?php echo $paypal_email; ?>" />
                    <input type="hidden" name="rm" value="2" />
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="currency_code" value="USD" />
                    <input type="hidden" name="notify_url" value="<?php echo $ipn; ?>" />
                    <input type="hidden" name="return" value="<?php echo home_url('/your-deal-transaction?item_id='.$buyId.'&user_id='.$current_user->ID); ?>" />
                    <input type="hidden" name="cancel_return" value="<?php echo home_url('/daily-deals'); ?>" />
                    <input type="hidden" name="item_name" value="<?php echo $itemName; ?>" />
                    <input type="hidden" name="item_number" value="<?php echo $buyId; ?>" />                    
                    <input type="hidden" name="amount" value="<?php echo $itemPrice; ?>" />
                </form>

                <script type="text/javascript">
                    jQuery(document).ready(function() {

                        jQuery('#paypal-js').click(function() {
                           jQuery('#paypal-form').submit();
                           return false;
                        });

                        window.setTimeout(function() {

                            jQuery('#paypal-form').submit();

                        },5000);

                    });
                </script>

            </article>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>