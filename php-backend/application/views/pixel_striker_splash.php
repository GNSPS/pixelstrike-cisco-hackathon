				<!-- Banner -->
					<section id="banner">
						<div class="inner">
							<h2>PixelStrike</h2>
                            
							<p><br/>Cops vs Cops vs Terrorists vs Terrorists</p>
                            <p>Who will win?</p>
                            <br/>
                            <br/>
                            <p>A Geolocation game that uses tropo and meraki triangulation to interact with the players</p>
<!--                            mas a <span style="font-weight:800">experiência</span>, sim.</p>-->
							<!--<ul class="actions">
								<li><a href="#" class="button special">Activate</a></li>
							</ul>-->
						</div>
						<a href="#one" class="more scrolly">Play</a>
					</section>

				<!-- One -->
					<section id="one" class="wrapper style1 special" >
						<div class="inner">
							<header class="major">
								<h2>Start playing now</h2>
								<p>To start playing PixelStrike <?php if(!empty($activation_code)) { ?>just call the following number: +351 308 801 746</p>
                                <p id="already_registered">Your pin code is: <span id="code" style="font-size: 1.5em; color: red; font-weight: bold;"><?php echo $activation_code ?></span><?php } else { ?> just join Pixels Camp "Meraki_Pixel" Wi-Fi network and you'll get an awesome PixelStrike splash page! :D<?php } ?></p>
                                <p>PS: You have to get the PIN on your phone or you won't be able to get a good experience.</p>
                                <p style="font-size: 1.5em">Leaderboard</p>
                                <p id="leaderBoard"><?php echo json_encode($leaderboard) ?></p>
                            </header>
						</div>
					</section>
    

				<!-- Two -->
					<section id="two" class="wrapper alt style2">
						<section class="spotlight">
							<div class="image"><img src="images/counter_terrorist.jpg" alt="" /></div><div class="content">
								<h2>Cop (aka. Counter Terrorist)</h2>
								<p>The Counter Terrorist's goal in the game is to defuse as many bombs as they can.</p>
								<p>They earn points based on the number of people surrounding the bombs at time of defusal </p>
							</div>
						</section>

						<section class="spotlight">
							<div class="image"><img src="images/terrorist.png" alt="" /></div><div class="content">
								<h2>Terrorist</h2>
								<p>The Terrorist's goal is to kill as many Pixel Campers as they can by placing bombs in crowded areas (Bombs explode in 2 mins)</p>
								<p>They earn points based on the number of people that are in a 5 meter radius from an exploding bomb.</p>
							</div>
						</section>
					</section>

<script>
    $(document).ready(function(){
        var jsonText = $('#leaderBoard').text();
        var json = JSON.parse(jsonText);
        $('#leaderBoard').empty();
        for(var i=0; i<json.length; i++){
            $('#leaderBoard').append('player: '+json[i].caller_id +' | score: '+ json[i].lives_actioned + '<br>' );
        }
    });
</script>