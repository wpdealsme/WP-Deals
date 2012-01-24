<?php

//secure included files
deals_secure();

/**
 * Run statistic
 * @return void
 */
function deals_stats() {    
    _deals_stats_view();    
}

/**
 * Manage request and output to screen
 * @return void
 */
function _deals_stats_view() {
    
    $tabs = array('sales' => 'Sales',
                  'download' => 'Download');
    
    $url = admin_url('/edit.php?post_type=daily-deals&page=deal-stats');
    
    ?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            
            <?php
            
            foreach($tabs as $tab => $anchor) {
                
                if(!isset($_GET['tab']) && $tab == 'sales') {
                    echo '<a class="nav-tab nav-tab-active" href="'.$url.'&tab='.$tab.'">'.$anchor.'</a>';   
                }else{
                    
                    if(isset($_GET['tab']) && $tab == $_GET['tab']) {
                        echo '<a class="nav-tab nav-tab-active" href="'.$url.'&tab='.$tab.'">'.$anchor.'</a>';   
                    }else{
                        echo '<a class="nav-tab" href="'.$url.'&tab='.$tab.'">'.$anchor.'</a>';   
                    }
                    
                }
                
            }
            
            ?>
            
        </h2>
        
        <?php
        
            if(!isset($_GET['tab'])) {
                _deals_stats_view_sales();
            }else{
                
                $function = 'stats_view_'.strtolower($_GET['tab']);
                if(function_exists($function)) {
                    call_user_func($function);
                }else{
                    echo 'requested action not available.';
                }
                
            }
        
        ?>
        
    </div>
    <?php
    
}

/**
 * Manage sales statistic
 * 
 * @return void
 */
function _deals_stats_view_sales() {
    
    global $wpdb;
    
    $table = $wpdb->prefix.'wpdeals_sales';
    if(!isset($_POST['submit'])) {
     
        $graph_title = 'Last 10 Sales';
        $data_overview_sql = 'SELECT DATE_FORMAT(buy_date,"%d-%m-%Y") AS newdate,SUM(total_price) AS sum_price FROM '.$table.' WHERE buy_date <= STR_TO_DATE("'.date('d-m-Y').'","%d-%m-%Y") GROUP BY newdate DESC LIMIT 10';
        $data_overview = $wpdb->get_results($data_overview_sql);
        
    }else{
    
        $graph_title = 'Data between '.$_POST['start_date'].' AND '.$_POST['end_date'].'';
        $data_overview_sql = 'SELECT DATE_FORMAT(buy_date,"%d-%m-%Y") AS newdate,SUM(total_price) AS sum_price FROM '.$table.' WHERE buy_date BETWEEN "'.$_POST['start_date'].'" AND "'.$_POST['end_date'].'" GROUP BY newdate DESC LIMIT 10';
        $data_overview = $wpdb->get_results($data_overview_sql);
        
    }        
    
    $data_total_sales_sql = $wpdb->prepare('SELECT COUNT(id) FROM '.$table.'');
    $data_total_sales = $wpdb->get_var($data_total_sales_sql);
    $data_total_sales = !empty($data_total_sales) ? $data_total_sales : 0;
    
    $data_total_money_sql = $wpdb->prepare('SELECT SUM(total_price) AS sum_price FROM '.$table);
    $data_total_money = $wpdb->get_var($data_total_money_sql);    
    $data_total_money = is_null($data_total_money) || empty($data_total_money) ? 0 : $data_total_money;    
    $data_total_money = deals_price_format($data_total_money);
    
    $data_top_sales_sql = $wpdb->prepare('SELECT id,item_id,COUNT(item_id) AS item_fav FROM '.$table.' GROUP BY item_id ORDER BY item_fav DESC LIMIT 1');
    $data_top_sales = $wpdb->get_row($data_top_sales_sql);
    $data_top_sales = !empty($data_top_sales) ? $data_top_sales : null;
    
    if(!is_null($data_top_sales)) {
        $item_props = get_post($data_top_sales->item_id);
        $data_top_sales_props = array('item_name' => $item_props->post_title,
                                      'item_permalink' => admin_url('?page=deal-sales-detail&sale_id='.$data_top_sales->id),
                                      'item_total_sales' => $data_top_sales->item_fav);
    }else{
        $data_top_sales_props = array('item_name' => null,
                                      'item_permalink' => null,
                                      'item_total_sales' => null);
    }
    
    $data_last_5_sales_sql = $wpdb->prepare('SELECT id,item_id FROM '.$table.' ORDER BY buy_date DESC LIMIT 5');
    $data_last_5_sales = $wpdb->get_results($data_last_5_sales_sql);
    
    $data_js = null;
    $total_ticks = 0;
    
    if(!empty($data_overview)) {
        
        $data_js_overview = array();
        foreach($data_overview as $dataO) {
            
            $date_js = date('d-M-y',strtotime($dataO->newdate));
            $data_js_overview[] = '["'.$date_js.'",'.$dataO->sum_price.']';
            
        }
        
        $data_js = join(',',$data_js_overview);
        $total_ticks = count($data_overview);
        
    }
    
    /*
     if total data just 1 it's not enough to create a graph
    */
    if($total_ticks == 1) {
        
        $exp_data_js = explode(',',$data_js);
        $date_current = str_replace(array('[','"'),'',current($exp_data_js));
        $exp_date_current = explode('-',$date_current);        
        $date_before = date('d-M-y', strtotime('yesterday',strtotime($date_current)));
        
        $data_js = '["'.$date_before.'",0],'.$data_js;
        $total_ticks = 2;
        
    }
    
    ?>
    <div id="wpdeals-stats-wrapper">
            
        <div class="wpdeals-stats-clear"></div>
        <div id="wpdeals-stats-left" class="wpdeals-stats-go-left">
            
            <div id="poststuff">
                
                <div class="postbox">
                    <h3>Date range</h3>
                    <div class="inside">
                        <form method="post" action="<?php echo admin_url('/edit.php?post_type=daily-deals&page=deal-stats'); ?>">
                            Start <input type="text" name="start_date" id="js-start-date" /><br />
                            End <input type="text" name="end_date" id="js-end-date" /><br />
                            <input type="submit" name="submit" value="Submit" />
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('#js-start-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });
                                jQuery('#js-end-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });  
                            });
                        </script>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3>Total Sales</h3>
                    <div class="inside">
                        <p><strong><?php echo $data_total_sales; ?> - <?php echo $data_total_money; ?></strong></p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3>Top Sales</h3>
                    <div class="inside">
                        <p>
                            <?php
                            if(!is_null($data_top_sales_props['item_name'])) {
                                echo '<a href="'.$data_top_sales_props['item_permalink'].'">'.$data_top_sales_props['item_name'].'</a> - <span>'.$data_top_sales_props['item_total_sales'].' sales</span>';
                            }else{
                                echo 'n/a';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3>Last 5 sales</h3>
                    <div class="inside">                                                
                        <?php
                        if(!empty($data_last_5_sales)) {
                            
                            echo '<ul>';
                            foreach($data_last_5_sales as $sales) {
                                
                                $items = get_post($sales->item_id);                            
                                echo '<li>
                                <a href="'.admin_url('?page=deal-sales-detail&sale_id='.$sales->id).'">'.$items->post_title.'</a></li>';
                                
                            }
                            echo '</ul>';
                            
                        }else{
                            echo '<p>n/a</p>';
                        }
                        ?>
                    </div>
                </div>
                
            </div>
            
        </div>
        <div id="wpdeals-stats-right" class="wpdeals-stats-go-left">
            <div id="poststuff">
                <div class="postbox">
                    <h3>Statistic</h3>
                    <div class="inside">
                        <div id="wpdeals-stats-graph" style="height:400px;">
                            <!-- jqplot -->
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                               
                               jQuery.jqplot.config.enablePlugins = true;
                               var dataplot = [<?php echo $data_js; ?>];
                               jQuery.jqplot('wpdeals-stats-graph',
                                             [dataplot],
                                             {title: '<?php echo $graph_title; ?>',                                             
                                             axes:{                                                
                                                xaxis:{ label:'Date',
                                                        renderer: jQuery.jqplot.DateAxisRenderer,
                                                        tickOptions: {
                                                            formatString: '%#m/%#d/%y'
                                                        },
                                                        numberTicks:<?php echo $total_ticks; ?>},
                                                yaxis:{label:'Sale($)',                                                        
                                                        labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer,
                                                        tickOptions: {
                                                            formatString: '$%.2f',
                                                            angle: -30,
                                                            labelPosition: 'middle'
                                                        }}}});
                               
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpdeals-stats-clear"></div>
        
    </div>
    <?php
    
}

/**
 * Manage download statistic
 * @return void
 */
function _deals_stats_view_download() {
    
    global $wpdb;
    
    $table = $wpdb->prefix.'wpdeals_download';
    if(!isset($_POST['submit'])) {
     
        $graph_title = 'Last 10 Download';
        $data_overview_sql = 'SELECT DATE_FORMAT(download_date, "%d-%m-%Y") AS newdate,COUNT(item_id) AS total_download FROM '.$table.' WHERE download_date <= STR_TO_DATE("'.date('d-m-Y').'","%d-%m-%Y") GROUP BY newdate DESC LIMIT 10';
        $data_overview = $wpdb->get_results($data_overview_sql);
        
    }else{
        
        $graph_title = 'Data between '.$_POST['start_date'].' AND '.$_POST['end_date'].'';
        $data_overview_sql = 'SELECT DATE_FORMAT(download_date, "%d-%m-%Y") AS newdate,COUNT(item_id) AS total_download FROM '.$table.' WHERE download_date BETWEEN "'.$_POST['start_date'].'" AND "'.$_POST['end_date'].'" GROUP BY newdate DESC LIMIT 10';        
        $data_overview = $wpdb->get_results($data_overview_sql);
        
    }        
    
    $data_total_dl_sql = $wpdb->prepare('SELECT COUNT(id) FROM '.$table.'');
    $data_total_dl = $wpdb->get_var($data_total_dl_sql);
    $data_total_dl = !empty($data_total_dl) ? $data_total_dl : 0;
    
    $data_top_dl_sql = $wpdb->prepare('SELECT id,item_id,COUNT(item_id) AS item_fav FROM '.$table.' GROUP BY item_id ORDER BY item_fav DESC LIMIT 1');
    $data_top_dl = $wpdb->get_row($data_top_dl_sql);
    $data_top_dl = !empty($data_top_dl) ? $data_top_dl : null;
    
    if(!is_null($data_top_dl)) {
        $item_props = get_post($data_top_dl->item_id);
        $data_top_dl_props = array('item_name' => $item_props->post_title,
                                    'item_permalink' => admin_url('post.php?post='.$data_top_dl->item_id.'&action=edit'),
                                    'item_total_download' => $data_top_dl->item_fav);
    }else{
        $data_top_dl_props = array('item_name' => null,
                                    'item_permalink' => null,
                                    'item_total_download' => null);
    }
    
    $data_last_5_dl_sql = $wpdb->prepare('SELECT id,item_id FROM '.$table.' ORDER BY download_date DESC LIMIT 5');
    $data_last_5_dl = $wpdb->get_results($data_last_5_dl_sql);
    
    $data_js = null;
    $total_ticks = 0;
    
    if(!empty($data_overview)) {
        
        $data_js_overview = array();
        foreach($data_overview as $dataO) {
            
            $date_js = date('d-M-y',strtotime($dataO->newdate));
            $data_js_overview[] = '["'.$date_js.'",'.$dataO->total_download.']';
            
        }
        
        $data_js = join(',',$data_js_overview);
        $total_ticks = count($data_overview);
        
    }        
    
    /*
     if total data just 1 it's not enough to create a graph
    */
    if($total_ticks == 1) {
        
        $exp_data_js = explode(',',$data_js);
        $date_current = str_replace(array('[','"'),'',current($exp_data_js));
        $exp_date_current = explode('-',$date_current);        
        $date_before = date('d-M-y', strtotime('yesterday',strtotime($date_current)));
        
        $data_js = '["'.$date_before.'",0],'.$data_js;
        $total_ticks = 2;
        
    }
    
    ?>
    <div id="wpdeals-stats-wrapper">
            
        <div class="wpdeals-stats-clear"></div>
        <div id="wpdeals-stats-left" class="wpdeals-stats-go-left">
            
            <div id="poststuff">
                
                <div class="postbox">
                    <h3>Date range</h3>
                    <div class="inside">
                        <form method="post" action="<?php echo admin_url('/edit.php?post_type=daily-deals&page=deal-stats&tab=download'); ?>">
                            Start <input type="text" name="start_date" id="js-start-date" /><br />
                            End <input type="text" name="end_date" id="js-end-date" /><br />
                            <input type="submit" name="submit" value="Submit" />
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('#js-start-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });
                                jQuery('#js-end-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });  
                            });
                        </script>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3>Total Download</h3>
                    <div class="inside">
                        <p><strong><?php echo $data_total_dl; ?></strong></p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3>Top Download</h3>
                    <div class="inside">
                        <p>
                            <?php                            
                            if(!is_null($data_top_dl_props['item_name'])) {
                                echo '<a href="'.$data_top_dl_props['item_permalink'].'">'.$data_top_dl_props['item_name'].'</a> - <span>'.$data_top_dl_props['item_total_download'].' downloads</span>';
                            }else{
                                echo 'n/a';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3>Last 5 Download</h3>
                    <div class="inside">                                                
                        <?php
                        if(!empty($data_last_5_dl)) {
                            
                            echo '<ul>';
                            foreach($data_last_5_dl as $dl) {
                                
                                $items = get_post($dl->item_id);
                                echo '<li>
                                <a href="'.admin_url('post.php?post='.$dl->item_id.'&action=edit').'">'.$items->post_title.'</a></li>';
                                
                            }
                            echo '</ul>';
                            
                        }else{
                            echo '<p>n/a</p>';
                        }
                        ?>
                    </div>
                </div>
                
            </div>
            
        </div>
        <div id="wpdeals-stats-right" class="wpdeals-stats-go-left">
            <div id="poststuff">
                <div class="postbox">
                    <h3>Statistic</h3>
                    <div class="inside">
                        <div id="wpdeals-dl-graph" style="height:400px;">
                            <!-- jqplot -->                            
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                               
                               jQuery.jqplot.config.enablePlugins = true;
                               var dataplot = [<?php echo $data_js; ?>];
                               jQuery.jqplot('wpdeals-dl-graph',
                                             [dataplot],
                                             {title: '<?php echo $graph_title; ?>',
                                             axes:{                                                
                                                xaxis:{ show: true,
                                                        label:'Date',                                                        
                                                        renderer: jQuery.jqplot.DateAxisRenderer,                                                        
                                                        tickOptions: {
                                                            formatString: '%#m/%#d/%y'
                                                        },
                                                        numberTicks:<?php echo $total_ticks; ?>},
                                                yaxis:{label:'Download(x)',
                                                        labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer,
                                                        tickOptions: {                                                            
                                                            angle: -30,
                                                            labelPosition: 'middle'
                                                        }}}});
                               
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpdeals-stats-clear"></div>
        
    </div>
    <?php
    
}