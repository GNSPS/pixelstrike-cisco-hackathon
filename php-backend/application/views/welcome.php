				<!-- Banner -->
					<section id="banner">
						<div class="inner">
							<h2>Silva &amp; Carvalhas</h2>
                            
							<p><br/>50+ anos</p>
                            <p>25+ profissionais</p>
                            <br/>
                            <br/>
                            <p>A idade não é um posto<br/>
                            mas a <span style="font-weight:800">experiência</span>, sim.</p>
							<!--<ul class="actions">
								<li><a href="#" class="button special">Activate</a></li>
							</ul>-->
						</div>
						<a href="#one" class="more scrolly">Descubra mais</a>
					</section>

				<!-- One -->
					<section id="one" class="wrapper style1 special">
						<div class="inner">
							<header class="major">
								<h2>Do saber ao fazer</h2>
								<p>Constituída desde Janeiro de 1960, fruto da experiência dos promotores no sector e do potencial de consumo de combustíveis e serviços relacionados com a manutenção de veículos, a Silva &amp; Carvalhas, Lda. coloca hoje à disposição dos seus clientes, 50 anos de experiência e saber fazer, com um vasto portefólio de produtos e serviços.</p>
							</header>
							<ul class="icons major">
								<li><a href="/posto_pneus" style="border-bottom: 0"><span class="icon fa-cog major style1" style="color:#666"></span><p class="label">Pneus e Serviços Auto</p></a></li>
								<li><a href="/servico_gas" style="border-bottom: 0"><span class="icon fa-fire major style1" style="color:#f99"></span><p class="label">Serviço Gás</p></a></li>
								<li><a href="/lubrificantes" style="border-bottom: 0"><span class="icon fa-flask major style1" style="color:#3288ff"></span><p class="label">Lubrificantes</p></a></li>
								<li><a href="/posto_abastecimento" style="border-bottom: 0"><span class="icon fa-car major style1" style="color:#9f9"></span><p class="label">Posto de Abastecimento</p></a></li>
<!--								<li><a href="/energias" style="border-bottom: 0"><span class="icon fa-leaf major style1"></span><p class="label">Energias Renováveis</p></a></li>-->
							</ul>
						</div>
					</section>
    
                <!-- News -->
					<section id="four" class="wrapper style3 special">
						<div class="inner" style="display:none;">
							<header class="major">
								<h2>Notícias</h2>
							</header>
                            <div id="news_holder">
                            
                            </div>
						</div>
					</section>

				<!-- Two -->
					<section id="two" class="wrapper alt style2">
						<section class="spotlight">
							<div class="image"><img src="images/fig_02.jpg" alt="" /></div><div class="content">
								<h2>Uma estória de história</h2>
								<p>O saber não ocupa lugar e nós já guardámos algum.</p>
							</div>
						</section>
						<!--<section class="spotlight">
							<div class="image"><img src="images/fig_06.jpg" alt="" /></div><div class="content">
								<h2>Tortor dolore feugiat<br />
								elementum magna</h2>
								<p>Aliquam ut ex ut augue consectetur interdum. Donec hendrerit imperdiet. Mauris eleifend fringilla nullam aenean mi ligula.</p>
							</div>
						</section>-->
						<section class="spotlight">
							<div class="image"><img src="images/posto_pneus3.jpg" alt="" /></div><div class="content">
								<h2>Não nos contentamos com o bom</h2>
								<p>A nossa preocupação com o cliente reflecte-se em não nos conformarmos em sermos apenas bons. Nós almejamos sempre ser os melhores.</p>
							</div>
						</section>
					</section>

				<!-- Three -->
					<section id="three" class="wrapper style3 special">
						<div class="inner">
							<header class="major">
								<h2>A capa faz o monge</h2>
								<p>Os objectivos que nos regem são o espelho da cultura da empresa. O nosso orgulho são os clientes.</p>
							</header>
							<ul class="features">
								<li class="icon fa-briefcase">
									<h3>Padrões</h3>
									<p>Garantimos elevados níveis de excelência dos produtos e serviços que comercializamos.</p>
								</li>
								<li class="icon fa-star-o">
									<h3>Fidelidade</h3>
									<p>Construimos uma relação de fidelidade com os seus clientes, sustentada no cumprimento rigoroso dos compromissos acordados.</p>
								</li>
								<li class="icon fa-paper-plane-o">
									<h3>Avanço</h3>
									<p>Mantemo-nos a par das melhores técnicas disponíveis para os nossos sectores de actividade, e sempre que oportuno, desenvolver e incorporar tecnologias e práticas adequadas.</p>
								</li>
								<li class="icon fa-flag-o">
									<h3>Esforço</h3>
									<p>Antecipamos as necessidades dos nossos clientes, proporcionando produtos e serviços capazes de ir ao encontro das suas expectativas.</p>
								</li>
								<li class="icon fa-eye">
									<h3>Visão</h3>
									<p>Estabelecer parcerias estratégicas com o objectivo de proporcionar aos nossos clientes as melhores soluções de mercado.</p>
								</li>
								<li class="icon fa-heart-o">
									<h3>Respeito</h3>
									<p>Cumprir com os requisitos legais no que respeita ao tratamento de resíduos gerados pela nossa actividade, promovendo um melhor ambiente.</p>
								</li>
							</ul>
						</div>
					</section>


				<!-- CTA -->
					<section id="cta" class="wrapper style4">
						<div class="inner">
							<header>
								<h2>Fale connosco</h2>
								<p>Pode entrar em contacto com um dos nossos colaboradores imediatamente através do chat no canto inferior direito.</p>
							</header>
							<ul class="actions vertical">
								<li style="vertical-align:bottom"><a href="#chat" onclick="toggleChat()" class="button fit special">Falar já</a></li>
								<!--<li><a href="#" class="button fit">Learn More</a></li>-->
							</ul>
						</div>
					</section>
<script>
    jQuery(document).ready(function(){
        $.get('https://dl.dropboxusercontent.com/s/rswp04zsmfvxamc/news.txt?dl=1', function(data){
            var converter = new showdown.Converter(),
            html = converter.makeHtml(data);
            $('#four #news_holder').append(html);
            $('#four .inner').show();
        });
    });
</script>