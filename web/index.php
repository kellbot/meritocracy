<html>
<head>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link href="https://jqwidgets.com/public/jqwidgets/styles/jqx.base.css" rel="stylesheet" />
<script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
<script src="https://jqwidgets.com/public/jqwidgets/jqx-all.js"></script>
  <script type="text/javascript">
  jQuery(document).ready( function($) {
  		ajaxurl = 'http://meritocracy/fetch_counter.php';

  		window.setInterval(function(){
		  $.get(ajaxurl,  function(response) {
		  		result = $.parseJSON(response);
				$('.merits').html(result.merits);

				$('#gaugeContainer').jqxGauge('value', result.cadence);
			});
		
		}, 200);

		

  });
  </script>
<style>
	@font-face {
    font-family: digital;
    src: url(Open24DisplayST.ttf);
	}
	body { margin : 0;}
	.wrapper {
	  width: 100%;
	  /* whatever width you want */
	  display: inline-block;
	  position: relative;
	}
	.wrapper:after {
	  padding-top: 56.25%;
	  /* 16:9 ratio */
	  display: block;
	  content: '';
	}
	#main {
		background: url("BikeBackground.jpg");
		background-repeat: no-repeat;
		background-size: 100%;
		width: 100%;
		height: 100%;
		max-width: 1280px;
		max-height: 720px;
		margin: auto;
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
	}
	#display {
		position: absolute;
		width: 408px;
		height: 60px;
		right: 10.9375%;
		bottom: 11.52%;
		background-color: rgba(0, 0, 0, 0.5);
		border-radius: 5px;
		font-family: digital;
		
		line-height: 50px;
		letter-spacing: .15em;
		text-align: right;
		border: 1px solid white;
	}
	.merits {
		font-size: 55px;
		position: absolute;
		right: 60px;
		color: #fff;
	}
	.logo {
		position: absolute;
		top: 4;
		right: 5;
		height: 50px;
		width: 50px;
		font-size: 13px;
	}
</style>
   <style type="text/css">
        #gaugeValue {
	        background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #fafafa), color-stop(100%, #f3f3f3));
	        background-image: -webkit-linear-gradient(#fafafa, #f3f3f3);
	        background-image: -moz-linear-gradient(#fafafa, #f3f3f3);
	        background-image: -o-linear-gradient(#fafafa, #f3f3f3);
	        background-image: -ms-linear-gradient(#fafafa, #f3f3f3);
	        background-image: linear-gradient(#fafafa, #f3f3f3);
	        -webkit-border-radius: 3px;
	        -moz-border-radius: 3px;
	        -ms-border-radius: 3px;
	        -o-border-radius: 3px;
	        border-radius: 3px;
	        -webkit-box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
	        -moz-box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
	        box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
	        padding: 10px;
	    }
    </style>

     <script type="text/javascript">
        $(document).ready(function () {
            $('#gaugeContainer').jqxGauge({
                ranges: [{ startValue: 0, endValue: 30, style: { fill: '#4bb648', stroke: '#4bb648' }, endWidth: 5, startWidth: 1 },
                         { startValue: 30, endValue: 60, style: { fill: '#fbd109', stroke: '#fbd109' }, endWidth: 10, startWidth: 5 },
                         { startValue: 60, endValue: 90, style: { fill: '#ff8000', stroke: '#ff8000' }, endWidth: 13, startWidth: 10 },
                         { startValue: 90, endValue: 120, style: { fill: '#e02629', stroke: '#e02629' }, endWidth: 16, startWidth: 13 }],
                ticksMinor: { interval: 5, size: '5%' },
                ticksMajor: { interval: 10, size: '9%' },
                value: 0,
                min: 0,
                max: 120,
                colorScheme: 'scheme05',
                animationDuration: 1200
            });
            $('#gaugeContainer').on('valueChanging', function (e) {
                $('#gaugeValue').text(Math.round(e.args.value) + ' crpm');
            });
        });
</script>
</head>
<div class="wrapper">
<div id="main">
	<div id='gaugeContainer'><div id="gaugeValue"></div></div>
	
	<div id="display">
		<span class="merits"></span>
		<div class="logo">
			<span class="fa-stack fa-2x fa-lg">
			  <i class="fa fa-circle-thin fa-stack-2x fa-inverse"></i>
			  <i class="fa fa-rub fa-stack-1x fa-inverse"></i>
			</span>
		</div>

	</div>
</div>
</div>