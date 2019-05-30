                <input type="hidden" datatable="pe.avvioproc" id="op_pe-avvioproc-online" class="search text" name="online" value="equal">
                <input type="hidden" value="1" id="1_pe-avvioproc-online" name="online" class="text">
                <table id="table-filter">
                    <tr id="flt-assegnata_istruttore">
                        <td valign="middle">
                            <label for="assegnata_istruttore" class="title">Istruttore assegnato</label><br/>
                            <input type="hidden" datatable="pe.vista_assegnate" id="op_pe-vista_assegnate-assegnata_istruttore" class="search text check" name="assegnata_istruttore" value="equal">                           
                            <input type="radio" value="0" id="1_pe-vista_assegante-assegnata_istruttore" name="assegnata_istruttore"  data-plugins="dynamic-search">
                            <label for="1_pe-vista_assegante-assegnata_istruttore" class="value">No</label><br/>
                            <input type="radio" value="1" id="1_pe-vista_assegante-assegnata_istruttore" name="assegnata_istruttore"  data-plugins="dynamic-search">
                            <label for="2_pe-vista_assegante-assegnata_istruttore" class="value">SI</label><br/>
                            <input type="radio" value="" id="1_pe-vista_assegante-assegnata_istruttore" name="assegnata_istruttore"  data-plugins="dynamic-search">
                            <label for="3_pe-vista_assegante-assegnata_istruttore" class="value">Tutte</label><br/>
                        </td>
                    </tr>
					<tr id="flt-sportello">
                        <td valign="middle">
                            <label for="sportello" class="title">Sportello di Presentazione</label><br/>
                            <input type="hidden" datatable="pe.avvioproc" id="op_pe-avvioproc-sportello" class="search text check" name="sportello" value="equal">                           
                            <input type="radio" value="SUE" id="1_pe-avvioproc-sportello" name="sportello"  data-plugins="dynamic-search">
                            <label for="1_pe-avvioproc-sportello" class="value">SUE</label><br/>
                            <input type="radio" value="SUAP" id="2_pe-avvioproc-sportello" name="sportello"  data-plugins="dynamic-search">
                            <label for="2_pe-avvioproc-sportello" class="value">SUAP</label><br/>
                            <input type="radio" value="%" id="3_pe-avvioproc-sportello" name="sportello"  data-plugins="dynamic-search">
                            <label for="3_pe-avvioproc-sportello" class="value">Tutti</label><br/>
                        </td>
                    </tr>
                </table>