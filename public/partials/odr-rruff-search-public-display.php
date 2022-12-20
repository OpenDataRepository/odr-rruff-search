<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://opendatarepository.org
 * @since      1.0.0
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/public/partials
 */
?>


<div id="rruff-search-form" class="search_form">
    <div class="input_wrapper first_input">
        <label for="txt_mineral">Mineral:</label>
        <input type="text" id="txt_mineral" name="txt_mineral" size="30" maxlength="255" value="" />
        <input type="hidden" id="mineral_ids" name="mineral_ids" value="">
        <input type="hidden" id="txt_tag_ids" name="txt_tag_ids" value="">
        <!-- <a class="page_link_1" href="Javascript:MM_openBrWindow('https://rruff.info/index.php/r=lookup_minerals/calling_form=frm_sample_search/name_field=txt_mineral','MineralLookup','scrollbars=yes,width=800,height=600')">lookup</a> -->
    </div>

    <div class="input_wrapper">
        <label for="txt_chemistry_incl">Chemistry Includes:
            <a class="chemistry_lookup_link">lookup</a>
        </label>
        <input type="text" id="txt_chemistry_incl" name="txt_chemistry_incl" value="" size="30" maxlength="255">
        <input type="hidden" id="chemistry_incl_txt">
    </div>

    <div class="input_wrapper">
        <label for="txt_chemistry_excl">Chemistry Excludes:
            <a class="chemistry_lookup_link">lookup</a>
        </label>
        <input type="text" id="txt_chemistry_excl" name="txt_chemistry_excl" value="" size="30" maxlength="255">
        <input type="hidden" id="chemistry_excl_txt">
    </div>

    <div class="input_wrapper">
        <label for="txt_general">General:</label>
        <input type="text" name="txt_general" value="" size="30" maxlength="255">
    </div>


    <div class="input_wrapper">
        <label for="sel_sort">Sort By:</label>
        <select name="sel_sort" size="1">
            <option value="name">Names</option>
            <option value="rruff_id">RRUFF ID</option>
            <option value="chemistry">Ideal Chemistry</option>
            <option value="source">Source</option>
            <option value="locality">Locality</option>
        </select>
        <select name="sel_sort_dir" size="1">
            <option value="asc">asc</option>
            <option value="desc">desc</option>
        </select>
    </div>

    <div class="input_wrapper submit_wrapper">
        <label for="submit"></label>
        <input id="rruff-search-form-submit" name="submit" type="submit" value="search">&nbsp;
        <input type="button" name="reset_sample_search" value="reset" onclick>
        <!-- <a class="page_link_1" href="#" onclick="new Effect.toggle('div_display_options','blind');return false;">display options</a> -->
    </div>
</div>

    <!-- <tr>
        <td colspan="3">
            <div id="div_display_options" style="overflow: visible;">
                <div id="div_display_options_contents" style="padding-top: 10px;">
                    <span class="title">Display Options</span><br>
                    <ul>
                        <li><input type="radio" id="display" name="display" value="default" checked="checked"> Default display - Name, RRUFF ID, Ideal Chemistry, Source, Locality.</li>
                        <li><input type="radio" id="display" name="display" value="picture"> Display pictures with the search results (limited to 100 per page).</li>
                        <li><input type="radio" id="display" name="display" value="raman"> Display Raman Spectra with the search results (limited to 100 per page).</li>
                        <li><input type="checkbox" id="save_as_default" name="save_as_default" value="true"> Save these options as your default search options.</li>
                        <li>
                            <script type="text/javascript">

                                function checkFilters(toggle) {
                                    if(toggle.checked) {
                                        $("unoriented_raman_filter_0").enable();
                                        $("unoriented_raman_filter_1").enable();
                                        $("unoriented_raman_filter_2").enable();
                                        $("unoriented_raman_filter_3").enable();
                                        $("unoriented_raman_filter_4").enable();
                                    }
                                    else {
                                        $("unoriented_raman_filter_0").disable();
                                        $("unoriented_raman_filter_1").disable();
                                        $("unoriented_raman_filter_2").disable();
                                        $("unoriented_raman_filter_3").disable();
                                        $("unoriented_raman_filter_4").disable();
                                    }
                                }

                            </script>
                            <input type="checkbox" id="unoriented_raman_filter_enabled" name="unoriented_raman_filter_enabled" value="false" onclick="checkFilters(this);">
                            Unoriented Raman Quality:&nbsp;&nbsp;&nbsp;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="unoriented_raman_filter_checkbox" type="checkbox" id="unoriented_raman_filter_0" name="unoriented_raman_filter[]" value="2" disabled=""> Excellent&nbsp;&nbsp;&nbsp;
                            <input class="unoriented_raman_filter_checkbox" type="checkbox" id="unoriented_raman_filter_1" name="unoriented_raman_filter[]" value="1" disabled=""> Fair&nbsp;&nbsp;&nbsp;
                            <input class="unoriented_raman_filter_checkbox" type="checkbox" id="unoriented_raman_filter_2" name="unoriented_raman_filter[]" value="0" disabled=""> Poor&nbsp;&nbsp;&nbsp;
                            <input class="unoriented_raman_filter_checkbox" type="checkbox" id="unoriented_raman_filter_3" name="unoriented_raman_filter[]" value="-1" disabled=""> Unrated&nbsp;&nbsp;&nbsp;
                            <input class="unoriented_raman_filter_checkbox" type="checkbox" id="unoriented_raman_filter_4" name="unoriented_raman_filter[]" value="-2" disabled=""> Ignore&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                checkFilters($("unoriented_raman_filter_enabled"));
                            </script>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
    </tr> -->

<div id="div_periodic_table" style="overflow: visible;">
    <div id="div_periodic_table_contents" style="min-height: 200px; padding-top: 10px;">
        <table id="rruff-periodic-table">
            <tbody><tr>
                <td><div style="display: block; text-align:center; cursor: pointer;  background:#a0ffa0;" class="periodic_table chem_ele_unselected" id="periodic_table_H">H</div></td>
                <td colspan="16" align="center" id="periodic_table_instructions">Click an element once to include, twice to exclude.</td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_He">He</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Li">Li</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Be">Be</div></td>
                <td colspan="1"></td>
                <td colspan="9">
                    <div style="width: 90%;text-align:center; cursor: pointer;  background:#ffdead;" class="periodic_table chem_ele_unselected" id="periodic_table_clear">Clear Chemistry</div>
                </td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_B">B</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_C">C</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_N">N</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_O">O</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_F">F</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Ne">Ne</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Na">Na</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Mg">Mg</div></td>
                <td colspan="1"></td>
                <td colspan="9">
                    <div style="width: 90%;text-align:center; cursor: pointer;  background:#ffdead;" class="periodic_table chem_ele_unselected" id="periodic_table_all">Exclude&nbsp;all&nbsp;non-selected</div>
                </td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Al">Al</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Si">Si</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_P">P</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_S">S</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_Cl">Cl</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Ar">Ar</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_K">K</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Ca">Ca</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Sc">Sc</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ti">Ti</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_V">V</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Cr">Cr</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Mn">Mn</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Fe">Fe</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Co">Co</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ni">Ni</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Cu">Cu</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Zn">Zn</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Ga">Ga</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Ge">Ge</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_As">As</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_Se">Se</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_Br">Br</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Kr">Kr</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Rb">Rb</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Sr">Sr</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Y">Y</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Zr">Zr</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Nb">Nb</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Mo">Mo</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Tc">Tc</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ru">Ru</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Rh">Rh</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Pd">Pd</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ag">Ag</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Cd">Cd</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_In">In</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Sn">Sn</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Sb">Sb</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Te">Te</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_I">I</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Xe">Xe</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Cs">Cs</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Ba">Ba</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffb888; font-style: italic;" class="periodic_table chem_ele_unselected" id="periodic_table_lanthanides" title="Shortcut for the lanthanide elements.">Ln</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Hf">Hf</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ta">Ta</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_W">W</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Re">Re</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Os">Os</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ir">Ir</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Pt">Pt</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Au">Au</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Hg">Hg</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Tl">Tl</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Pb">Pb</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Bi">Bi</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Po">Po</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_At">At</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Rn">Rn</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Fr">Fr</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Ra">Ra</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ff99cc; font-style: italic;" class="periodic_table chem_ele_unselected" id="periodic_table_actinides" title="Shortcut for the actinide elements.">An</div></td>
                <!-- <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Rf" >104<br />Rf</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Db" >105<br />Db</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Sg" >106<br />Sg</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Bh" >107<br />Bh</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Hs" >108<br />Hs</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Mt" >109<br />Mt</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Ds" >110<br />Ds</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Ra" >111<br />Rg</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uub" >112<br />Uub</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uut" >113<br />Uut</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uuq" >114<br />Uuq</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uup" >115<br />Uup</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uuh" >116<br />Uuh</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffff99;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uus" >117<br />Uus</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#c0ffff;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uuo">118<br />Uuo</div></td> -->
            </tr>
            <tr>
                <td colspan="2" style="text-align:right; font-size: 10px;">&nbsp;</td>
                <td><div style="text-align:center; cursor: pointer; font-style: italic;" class="periodic_table" id="periodic_table_lanthanides_alt" title="Shortcut for the lanthanide elements.">Ln</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_La">La</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Ce">Ce</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Pr">Pr</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Nd">Nd</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Pm">Pm</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Sm">Sm</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Eu">Eu</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Gd">Gd</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Tb">Tb</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Dy">Dy</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Ho">Ho</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Er">Er</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Tm">Tm</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Yb">Yb</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Lu">Lu</div></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right; font-size: 10px;">&nbsp;</td>
                <td><div style="text-align:center; cursor: pointer; font-style: italic;" class="periodic_table" id="periodic_table_actinides_alt" title="Shortcut for the actinide elements.">An</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Ac">Ac</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Th">Th</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Pa">Pa</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_U">U</div></td>
                <!-- <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Np" >Np</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Pu" >94<br />Pu</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Am" >95<br />Am</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Cu" >96<br />Cm</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Bk" >97<br />Bk</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Cf" >98<br />Cf</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Es" >99<br />Es</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Fm" >100<br />Fm</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Md" >101<br />Md</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_No" >102<br />No</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Lr" >103<br />Lr</div></td> -->
            </tr>
            </tbody>
        </table>
   </div>
</div>

<center><a href="#" onclick="new Effect.toggle('disclaimer','blind'); return false;">Terms and Conditions</a></center>