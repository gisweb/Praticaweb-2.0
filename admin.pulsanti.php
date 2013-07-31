<!-- PRIMA RIGA DI PULSANTI-->
			<div id="ParaToolbar">
				<select id="ParagraphStyle" class="stiletabella" onchange="formatC('formatBlock',this[this.selectedIndex].value);this.selectedIndex=0">
					<option selected>Paragrafo
					<option value="&lt;H1&gt;">Titolo 1 &lt;H1&gt;
					<option value="&lt;H2&gt;">Titolo 2 &lt;H2&gt;
					<option value="&lt;H3&gt;">Titolo 3 &lt;H3&gt;
					<option value="&lt;H4&gt;">Titolo 4 &lt;H4&gt;
					<option value="&lt;H5&gt;">Titolo 5 &lt;H5&gt;
					<option value="&lt;H6&gt;">Titolo 6 &lt;H6&gt;
					<option value="&lt;PRE&gt;">Formattato &lt;PRE&gt;
				</select>
				<select id="FontName" class="stiletabella" onchange="formatC('fontname',this[this.selectedIndex].value);this.selectedIndex=0">
					<option class="heading" selected>Tipo Carattere
					<option value="Arial">Arial
					<option value="Arial Black">Arial Black
					<option value="Arial Narrow">Arial Narrow
					<option value="Comic Sans MS">Comic Sans MS
					<option value="Courier New">Courier New
					<option value="System">System
					<option value="Times New Roman">Times New Roman
					<option value="Verdana">Verdana
					<option value="Wingdings">Wingdings
				</select>
				<select id="FontSize" class="stiletabella" onchange="formatC('fontsize',this[this.selectedIndex].value);this.selectedIndex=0">
					<option class="heading" selected>Dimensione
					<option value="1">1
					<option value="2">2
			    	<option value="3">3
				    <option value="4">4
				    <option value="5">5
				    <option value="6">6
				    <option value="7">7
				</select>
		    	<select id="FontColor" class="stiletabella" onchange="formatC('forecolor',this[this.selectedIndex].value);this.selectedIndex=0">
			    	<option class="heading" selected>Colore Testo
				    <option value="red">rosso
				    <option value="blue">blu
				    <option value="green">verde
				    <option value="black">nero
				</select>
			   	<select id="FontBackColor" class="stiletabella" onchange="formatC('backcolor',this[this.selectedIndex].value);this.selectedIndex=0">
			    	<option class="heading" selected>Colore Sfondo
				    <option value="red">rosso
				    <option value="blue">blu
				    <option value="green">verde
				    <option value="black">nero
			    	<option value="yellow">giallo
				    <option value="">BIANCO
				</select>
				<?if (!$_REQUEST["stampe"]){?>
				<select name="viste" class="stiletabella" id="viste">
				<?=$option_viste?>
				</select>
				<!--<select name="colonne" id="colonne" onchange="insert_tag('C',this[this.selectedIndex].value);this.selectedIndex=0">
				</select>>-->
				<input type="button" class="hexfield" name="" value="Seleziona Campo -->" onclick="((viste.selectedIndex==0)?(alert('Selezionare una vista')):(get_elenco_stampe('viste',viste.options[viste.selectedIndex].value)))">
				<input type="button" class="hexfield" name="" value="Seleziona Funzione-->" onclick="get_elenco_stampe('funzioni','')">
				<input type="button" class="hexfield" name="" value="Inserisci Ciclo" onclick="struct()">
				<?}?>
<!-- SECONDA RIGA DI PULSANTI-->				
				<hr>
				<div id="EditMode">
					<input type=checkbox name="switchMode" id="switchMode" onclick="setMode(switchMode.checked)">
					<font color="#000000"><b>Visualizza HTML</b> | </font>
					<a href="Javascript:formatC('removeFormat')"><b>Rimuovi Formattazione Carattere</b></a>
				</div>
				<hr>
			</div>
<!-- TERZARIGA DI PULSANTI-->
			<table>
				<tr>
					<td>
						<div title="Grassetto" onclick="formatC('bold');">
						    <img src="images/bold.gif">
						</div>
					</td>
					<td>
						<div title="Corsivo" onclick="formatC('italic')">
						    <img src="images/italic.gif">
						</div>
					</td>
				    <td>
						<div title="Sottolineato" onclick="formatC('underline')">
						    <img src="images/under.gif">
					    </div>
					</td>
					<td>
						<div title="Allinea a sinistra" onclick="formatC('justifyleft')">
						    <img src="images/aleft.gif">
					    </div>
					</td>
					<td>
						<div title="Centra" onclick="formatC('justifycenter')">
						    <img src="images/center.gif">
						</div>
					</td>
					<td>
						<div title="Allinea a destra" onclick="formatC('justifyright')">
						    <img src="images/aright.gif">
						</div>
					</td>
					<td>
						<div title="Elenco puntato" onclick="formatC('insertorderedlist')">
						    <img src="images/nlist.gif">
					    </div>
					</td>
					<td>
						<div title="Elenco numerato" onclick="formatC('insertunorderedlist')">
						    <img src="images/blist.gif">
					  	</div>
					</td>
					<td>
						<div title="Riduci rientro" onclick="indenta('S')">
						    <img  src="images/ileft.gif">
						</div>
					</td>
					<td>
						<div title="Aumenta rientro" onclick="indenta('D')">
						    <img src="images/iright.gif" >
						</div>
					</td>
					<td>
						<div id="taglia" title="Taglia" onclick="formatC('cut')">
					    	<img src="images/cut.gif">
					 	</div>
					</td>
					<td>
						<div id="copia" title="Copia"  onclick="formatC('copy')">
						    <img src="images/copy.gif">
  						</div>
					</td>
				  	<td>
						<div id="incolla" title="Incolla" onclick="formatC('paste')">
						    <img src="images/paste.gif">
					    </div>
					</td>
			  	</tr>
			</table>