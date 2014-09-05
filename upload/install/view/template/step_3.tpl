<?php echo $header; ?>

<h1><?php echo $this->config->get('LANG_STEP_3_TITLE')?></h1>
<div class="navigation">
    <div class="progress progress-striped">
        <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
            <span class="sr-only">75% Complete</span>
        </div>
    </div>

    <ul class="steps">
        <li><?php echo $this->config->get('LANG_STEP_1_SHORT_TITLE')?></li>
        <li><?php echo $this->config->get('LANG_STEP_2_SHORT_TITLE')?></li>
        <li><strong><?php echo $this->config->get('LANG_STEP_3_SHORT_TITLE')?></strong></li>
        <li><?php echo $this->config->get('LANG_STEP_4_SHORT_TITLE')?></li>
    </ul>
</div>
<div id="content">
    <?php if (isset($error)):  ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form" itsok="noitsnot">
        <p><?php echo $this->config->get('LANG_STEP_3_PART_1')?></p>
        <fieldset id="database">
            <input type="hidden" name="action" value="dbcheck">
            <input type="hidden" name="db_driver" id="db_driver" value="mysql">
            <div class="form-group">
                <label for="db_host" class="control-label col-sm-3"><?php echo $this->config->get('LANG_HOSTNAME')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" class="form-control" name="db_host" id="db_host" value="<?php echo $db_host; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="db_user" class="control-label col-sm-3"><?php echo $this->config->get('LANG_USERNAME')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" class="form-control" name="db_user" id="db_user" value="<?php echo $db_user; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="db_password" class="control-label col-sm-3"><?php echo $this->config->get('LANG_PASSWORD')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" class="form-control" name="db_password" id="db_password" value="<?php echo $db_password; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="db_name" class="control-label col-sm-3"><?php echo $this->config->get('LANG_DATABASE')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" class="form-control" name="db_name" id="db_name" value="<?php echo $db_name; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="db_prefix" class="control-label col-sm-3"><?php echo $this->config->get('LANG_PREFIX')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" class="form-control" name="db_prefix" id="db_prefix" value="<?php echo $db_prefix; ?>" readonly />
                </div>
            </div>
        </fieldset>
        <p><?php echo $this->config->get('LANG_STEP_3_PART_2')?></p>
        <fieldset id="account">
            <input type="hidden" name="action" value="account">
            <div class="form-group">
                <label for="username" class="control-label col-sm-3"><?php echo $this->config->get('LANG_USERNAME')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $username; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="control-label col-sm-3"><?php echo $this->config->get('LANG_PASSWORD')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" id="password" name="password" class="form-control" value="<?php echo $password; ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="control-label col-sm-3">Email:</label>
                <div class="control-group col-sm-9">
                    <input type="text" id="email" name="email" class="form-control" value="<?php echo $email; ?>" />
                </div>
            </div>
        </fieldset>

        <p><?php echo $this->config->get('LANG_STEP_3_PART_3')?></p>
        <fieldset id="store">
            <input type="hidden" name="action" value="store">
            <div class="form-group">
                <label for="license_key" class="control-label col-sm-3"><?php echo $this->config->get('LANG_LICENSE_KEY')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" name="license_key" class="form-control" value="<?php echo $license_key?>">
                    <br /><div class="alert alert-info"><?php echo $this->config->get('LANG_LICENSE_INFO')?></div>
                </div>
            </div>
            <div class="form-group">
                <label for="store_name" class="control-label col-sm-3"><?php echo $this->config->get('LANG_STORE_NAME')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" name="store_name" class="form-control" value="<?php echo $store_name?>">
                </div>
            </div>
            <div class="form-group">
                <label for="store_email" class="control-label col-sm-3"><?php echo $this->config->get('LANG_STORE_MAIL')?>:</label>
                <div class="control-group col-sm-9">
                    <input type="text" name="store_email" class="form-control" value="<?php echo $store_email?>">
                </div>
            </div>
            <div class="form-group">
                <label for="country_id" class="control-label col-sm-3"><?php echo $this->config->get('LANG_COUNTRY')?>:</label>
                <div class="control-group col-sm-9">
                    <select id="country" name="country_id" class="form-control">
                        <option value="1">Afghanistan</option><option value="2">Albania</option><option value="3">Algeria</option><option value="4">American Samoa</option><option value="5">Andorra</option><option value="6">Angola</option><option value="7">Anguilla</option><option value="8">Antarctica</option><option value="9">Antigua and Barbuda</option><option value="10">Argentina</option><option value="11">Armenia</option><option value="12">Aruba</option><option value="13">Australia</option><option value="14">Austria</option><option value="15">Azerbaijan</option><option value="16">Bahamas</option><option value="17">Bahrain</option><option value="18">Bangladesh</option><option value="19">Barbados</option><option value="20">Belarus</option><option value="21">Belgium</option><option value="22">Belize</option><option value="23">Benin</option><option value="24">Bermuda</option><option value="25">Bhutan</option><option value="26">Bolivia</option><option value="27">Bosnia and Herzegowina</option><option value="28">Botswana</option><option value="29">Bouvet Island</option><option value="30">Brazil</option><option value="31">British Indian Ocean Territory</option><option value="32">Brunei Darussalam</option><option value="33">Bulgaria</option><option value="34">Burkina Faso</option><option value="35">Burundi</option><option value="36">Cambodia</option><option value="37">Cameroon</option><option value="38">Canada</option><option value="39">Cape Verde</option><option value="40">Cayman Islands</option><option value="41">Central African Republic</option><option value="42">Chad</option><option value="43">Chile</option><option value="44">China</option><option value="45">Christmas Island</option><option value="46">Cocos (Keeling) Islands</option><option value="47">Colombia</option><option value="48">Comoros</option><option value="49">Congo</option><option value="50">Cook Islands</option><option value="51">Costa Rica</option><option value="52">Cote D'Ivoire</option><option value="53">Croatia</option><option value="54">Cuba</option><option value="55">Cyprus</option><option value="56">Czech Republic</option><option value="237">Democratic Republic of Congo</option><option value="57">Denmark</option><option value="58">Djibouti</option><option value="59">Dominica</option><option value="60">Dominican Republic</option><option value="61">East Timor</option><option value="62">Ecuador</option><option value="63">Egypt</option><option value="64">El Salvador</option><option value="65">Equatorial Guinea</option><option value="66">Eritrea</option><option value="67">Estonia</option><option value="68">Ethiopia</option><option value="69">Falkland Islands (Malvinas)</option><option value="70">Faroe Islands</option><option value="71">Fiji</option><option value="72">Finland</option><option value="73">France</option><option value="74">France, Metropolitan</option><option value="75">French Guiana</option><option value="76">French Polynesia</option><option value="77">French Southern Territories</option><option value="126">FYR of Macedonia</option><option value="78">Gabon</option><option value="79">Gambia</option><option value="80">Georgia</option><option value="81">Germany</option><option value="82">Ghana</option><option value="83">Gibraltar</option><option value="84">Greece</option><option value="85">Greenland</option><option value="86">Grenada</option><option value="87">Guadeloupe</option><option value="88">Guam</option><option value="89">Guatemala</option><option value="90">Guinea</option><option value="91">Guinea-bissau</option><option value="92">Guyana</option><option value="93">Haiti</option><option value="94">Heard and Mc Donald Islands</option><option value="95">Honduras</option><option value="96">Hong Kong</option><option value="97">Hungary</option><option value="98">Iceland</option><option value="99">India</option><option value="100">Indonesia</option><option value="101">Iran (Islamic Republic of)</option><option value="102">Iraq</option><option value="103">Ireland</option><option value="104">Israel</option><option value="105">Italy</option><option value="106">Jamaica</option><option value="107">Japan</option><option value="108">Jordan</option><option value="109">Kazakhstan</option><option value="110">Kenya</option><option value="111">Kiribati</option><option value="113">Korea, Republic of</option><option value="114">Kuwait</option><option value="115">Kyrgyzstan</option><option value="116">Lao People's Democratic Republic</option><option value="117">Latvia</option><option value="118">Lebanon</option><option value="119">Lesotho</option><option value="120">Liberia</option><option value="121">Libyan Arab Jamahiriya</option><option value="122">Liechtenstein</option><option value="123">Lithuania</option><option value="124">Luxembourg</option><option value="125">Macau</option><option value="127">Madagascar</option><option value="128">Malawi</option><option value="129">Malaysia</option><option value="130">Maldives</option><option value="131">Mali</option><option value="132">Malta</option><option value="133">Marshall Islands</option><option value="134">Martinique</option><option value="135">Mauritania</option><option value="136">Mauritius</option><option value="137">Mayotte</option><option value="138">Mexico</option><option value="139">Micronesia, Federated States of</option><option value="140">Moldova, Republic of</option><option value="141">Monaco</option><option value="142">Mongolia</option><option value="143">Montserrat</option><option value="144">Morocco</option><option value="145">Mozambique</option><option value="146">Myanmar</option><option value="147">Namibia</option><option value="148">Nauru</option><option value="149">Nepal</option><option value="150" selected="selected">Netherlands</option><option value="151">Netherlands Antilles</option><option value="152">New Caledonia</option><option value="153">New Zealand</option><option value="154">Nicaragua</option><option value="155">Niger</option><option value="156">Nigeria</option><option value="157">Niue</option><option value="158">Norfolk Island</option><option value="112">North Korea</option><option value="159">Northern Mariana Islands</option><option value="160">Norway</option><option value="161">Oman</option><option value="162">Pakistan</option><option value="163">Palau</option><option value="164">Panama</option><option value="165">Papua New Guinea</option><option value="166">Paraguay</option><option value="167">Peru</option><option value="168">Philippines</option><option value="169">Pitcairn</option><option value="170">Poland</option><option value="171">Portugal</option><option value="172">Puerto Rico</option><option value="173">Qatar</option><option value="174">Reunion</option><option value="175">Romania</option><option value="176">Russian Federation</option><option value="177">Rwanda</option><option value="178">Saint Kitts and Nevis</option><option value="179">Saint Lucia</option><option value="180">Saint Vincent and the Grenadines</option><option value="181">Samoa</option><option value="182">San Marino</option><option value="183">Sao Tome and Principe</option><option value="184">Saudi Arabia</option><option value="185">Senegal</option><option value="236">Serbia</option><option value="186">Seychelles</option><option value="187">Sierra Leone</option><option value="188">Singapore</option><option value="189">Slovak Republic</option><option value="190">Slovenia</option><option value="191">Solomon Islands</option><option value="192">Somalia</option><option value="193">South Africa</option><option value="194">South Georgia &amp; South Sandwich Islands</option><option value="195">Spain</option><option value="196">Sri Lanka</option><option value="197">St. Helena</option><option value="198">St. Pierre and Miquelon</option><option value="199">Sudan</option><option value="200">Suriname</option><option value="201">Svalbard and Jan Mayen Islands</option><option value="202">Swaziland</option><option value="203">Sweden</option><option value="204">Switzerland</option><option value="205">Syrian Arab Republic</option><option value="206">Taiwan</option><option value="207">Tajikistan</option><option value="208">Tanzania, United Republic of</option><option value="209">Thailand</option><option value="210">Togo</option><option value="211">Tokelau</option><option value="212">Tonga</option><option value="213">Trinidad and Tobago</option><option value="214">Tunisia</option><option value="215">Turkey</option><option value="216">Turkmenistan</option><option value="217">Turks and Caicos Islands</option><option value="218">Tuvalu</option><option value="219">Uganda</option><option value="220">Ukraine</option><option value="221">United Arab Emirates</option><option value="222">United Kingdom</option><option value="223">United States</option><option value="224">United States Minor Outlying Islands</option><option value="225">Uruguay</option><option value="226">Uzbekistan</option><option value="227">Vanuatu</option><option value="228">Vatican City State (Holy See)</option><option value="229">Venezuela</option><option value="230">Viet Nam</option><option value="231">Virgin Islands (British)</option><option value="232">Virgin Islands (U.S.)</option><option value="233">Wallis and Futuna Islands</option><option value="234">Western Sahara</option><option value="235">Yemen</option><option value="238">Zambia</option><option value="239">Zimbabwe</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="zone_id" class="control-label col-sm-3"><?php echo $this->config->get('LANG_ZONE')?>:</label>
                <div class="control-group col-sm-9">
                    <select id="zone" name="zone_id" class="form-control"></select>
                </div>
            </div>
            <div class="form-group">
                <label for="category" class="control-label col-sm-3"><?php echo $this->config->get('LANG_CATEGORY')?>:</label>
                <div class="control-group col-sm-9">
                    <select id="category" class="form-control" name="category">
                        <option value="1" >Appliances</option>
                        <option value="2" >Baby Goods/Kids Goods</option>
                        <option value="3" >Bags/Luggage</option>
                        <option value="4" >Board Game</option>
                        <option value="5" >Building Materials</option>
                        <option value="6" >Camera/Photo</option>
                        <option value="7" >Cars</option>
                        <option value="8" >Clothing</option>
                        <option value="9" >Commercial Equipment</option>
                        <option value="10" >Computers</option>
                        <option value="11" >Drugs</option>
                        <option value="12" >Electronics</option>
                        <option value="13" >Food/Beverages</option>
                        <option value="14" >Furniture</option>
                        <option value="15" >Games/Toys</option>
                        <option value="16" >Health/Beauty</option>
                        <option value="17" >Home Decor</option>
                        <option value="18" >Household Supplies</option>
                        <option value="19" >Jewelry/Watches</option>
                        <option value="20" >Kitchen/Cooking</option>
                        <option value="21" >Office Supplies</option>
                        <option value="22" >Outdoor Gear/Sporting Goods</option>
                        <option value="23" >Patio/Garden</option>
                        <option value="24" >Pet Supplies</option>
                        <option value="25" >Phone/Tablet</option>
                        <option value="26" >Product/Service</option>
                        <option value="27" >Software</option>
                        <option value="28" >Tools/Equipment</option>
                        <option value="29" >Video Game</option>
                        <option value="30" >Vitamins/Supplements</option>
                        <option value="31" >Website</option>
                        <option value="32" >Wine/Spirits</option>
                    </select>
                </div>
            </div>
            <div id="message">

            </div>
        </fieldset>
        <div class="buttons">
            <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $this->config->get('LANG_PREVIOUS_STEP')?></a></div>
            <div class="pull-right">
                <span class="btn btn-primary" id="install"><?php echo $this->config->get('LANG_NEXT_STEP')?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<script type="text/javascript">
$(function(){
    $('#country').on('change', function(){
        var country_id = $('option:selected', this).val();
        if (country_id == 150) {
            var options = '<option value="2329">Drenthe</option><option value="2330">Flevoland</option><option value="2331">Friesland</option><option value="2332">Gelderland</option><option value="2333">Groningen</option><option value="2334">Limburg</option><option value="2335">Noord-Brabant</option><option value="2336">Noord Holland</option><option value="2337">Overijssel</option><option value="2338">Utrecht</option><option value="2339">Zeeland</option><option value="2340" selected="selected">Zuid Holland</option>';
        }
        else if (country_id == 21) {
            var options = '<option value="344">Antwerpen</option><option value="352">Vlaams-Brabant</option><option value="348">Limburg</option><option value="351">Oost-Vlaanderen</option><option value="353">West-Vlaanderen</option><option value="345">Brabant Wallon</option><option value="346">Hainaut</option><option value="347">Liège</option><option value="349">Luxembourg</option><option value="350">Namur</option>';
        }
        else {
            var options = '<option value="0"><?php echo $this->config->get('LANG_ZONE_VIA_ADMIN')?></option>'
        }
        $('#zone').html(options);
    }).trigger('change');

    var submitted = true;
    $('#install').on('click', function(){
        var success = false;
        $('#message').html('');
        $('#install').addClass('disabled');

        // Run checks 1,2,3
        $.post('?route=step_3/ajax',
            $('#form').serialize()
        , function(data){
            if (data != 'OK') {
                data = $.parseJSON(data);
                submitted = false;
                $.each(data, function (k, value) {
                    $('#message').append('<div class="alert alert-danger">' + value + '</div>');
                });
            } else {
                success = true;
            }
        })
        setTimeout(function(){
            if (success) {
                $('#form').attr('itsok', 'itsallright').submit();
            }
            else {
                $('#form').attr('itsok', 'noitsnot');
                $('#install').removeClass('disabled');
            }
        }, 2500);
    });
    $('#form').on('submit', function(e) {
        if ($('#form').attr('itsok') != 'itsallright') {
            e.preventDefault();
            return false;
        }
        $('#form').slideUp(function(){
            $('#form').html('<div class="col-sm-12 text-center"><img src="view/image/spinner.gif" alt="Loading"></div>').show();
        })
    })
})
</script>
<?php echo $footer; ?>
