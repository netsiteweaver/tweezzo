<div class="row" id="modalAddCustomer">
    <div class="col-md-12">
        <!-- <div class="">
            <div class="form-group">
                <label>Customer Code</label>
                <input type="text" class="form-control" name="customer_code" id="customer_code"
                    placeholder="Auto Generated" value="" readonly>
            </div>
        </div> -->
        <div class="">
            <div class="form-group">
                <label>Gender: </label>
                <label for="gender_m"><input type="radio" name="gender" value="m" id="gender_m" checked autofocus>
                    Male</label>
                <label for="gender_f"><input type="radio" name="gender" value="f" id="gender_f"> Female</label>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Title: </label>
                <label for="title_mr"><input type="radio" name="title" value="Mr" id="title_mr" checked> Mr</label>
                <label for="title_mrs"><input type="radio" name="title" value="Mrs" id="title_mrs" disabled> Mrs</label>
                <label for="title_miss"><input type="radio" name="title" value="Miss" id="title_miss" disabled>
                    Miss</label>
                <label for="title_dr"><input type="radio" name="title" value="Dr" id="title_dr"> Dr</label>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Customer Code</label>
                <input type="text" class="form-control required" name="customer_code" id="customer_code" placeholder=""
                    value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" class="form-control required" name="first_name" id="first_name" placeholder=""
                    value="" required>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" id="lname" placeholder="" value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" id="address" cols="30" rows="4" class="form-control" placeholder=""></textarea>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>City, Village or Region</label>
                <input type="text" class="form-control" name="city" id="city" placeholder="" value="">
            </div>
        </div>

        <div class="">
            <div class="form-group">
                <label>NIC *</label>
                <input type="text" class="form-control required" name="nic" id="nic" minlength='14' maxlength='14' placeholder=""
                    value="" required>
            </div>
        </div>

        <div class="">
            <div class="form-group">
                <label>BRN</label>
                <input type="text" class="form-control" name="brn" id="brn" placeholder=""
                    value="" required>
            </div>
        </div>

        <div class="">
            <div class="form-group">
                <label>VAT</label>
                <input type="text" class="form-control" name="vat" id="vat" placeholder=""
                    value="" required>
            </div>
        </div>

        <div class="">
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" class="form-control" name="dob" id="dob" placeholder="" value="" min="1900-01-01"
                    max="<?php echo date("Y-m-d");?>" pattern="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Nationality</label>
                <select name="nationality" id="nationality" class="form-control">
                    <?php foreach($nationalities as $n):?>
                    <option value="<?php echo $n;?>" <?php echo ($n=="Mauritian")?'selected':'';?>><?php echo $n;?>
                    </option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Profession</label>
                <input type="text" class="form-control" name="profession" id="profession" placeholder=""
                    value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Marital Status: </label>
                <label for="ms_1"><input type="radio" name="marital_status" id="ms_1" value="single" checked>
                    Single</label>
                <label for="ms_2"><input type="radio" name="marital_status" id="ms_2" value="married"> Married</label>
                <label for="ms_3"><input type="radio" name="marital_status" id="ms_3" value="divorced"> Divorced</label>
                <label for="ms_4"><input type="radio" name="marital_status" id="ms_4" value="separated">
                    Separated</label>
                <label for="ms_5"><input type="radio" name="marital_status" id="ms_5" value="widowed"> Widowed</label>
            </div>
        </div>

        <div class="">
            <div class="form-group">
                <label>Shoe Size</label>
                <input type="text" class="form-control" name="shoe_size" id="shoe_size" placeholder=""
                    value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Clothes Size</label>
                <input type="text" class="form-control" name="clothes_size" id="clothes_size" placeholder=""
                    value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Height</label>
                <input type="text" class="form-control" name="height" id="height" placeholder="" value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Sports</label>
                <input type="text" class="form-control" name="sports" id="sports" placeholder="" value="">
            </div>
        </div>
        <div class="hidden">
            <div class="form-group">
                <label>Fidelity Card</label>
                <input type="text" class="form-control" name="fidelity_card" id="fidelity_card" placeholder=""
                    value="" readonly>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control" placeholder=""></textarea>
            </div>
        </div>

        <div class="">
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="" value="">
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Primary Phone Number</label>
                <input type="tel" pattern="[0-9]{7,8}" class="form-control" name="phone_number1" maxlength="8"
                    id="phone1" placeholder="7 to 8 numbers" value="" required>
            </div>
        </div>
        <div class="">
            <div class="form-group">
                <label>Secondary Phone Number</label>
                <input type="tel" pattern="[0-9]{7,8}" class="form-control" name="phone_number2" maxlength="8"
                    id="phone2" placeholder="7 to 8 numbers" value="">
            </div>
        </div>
    </div>
</div>