{include file="header.tpl"}

<h3>Add a new activity</h3>
<form role="form" action="/createActivity" method="post">
  <div class="mb-3">
    <label for="activityName" class="form-label">Activity name</label>
    <input type="text" class="form-control" id="activityName" name="activityName" placeholder="activity name" required autofocus maxlength="100">
    <div class="color-container" id="color-container">
        <div class="color-option">
            <input type="radio" id="color0" name="color" value="default" class="color-input" checked required>
            <label for="color0" class="color-label" style="background-color: #f5f5f5;" title="Light grey"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color1" name="color" value="pink" class="color-input" >
            <label for="color1" class="color-label" style="background-color: #f5e6e8;" title="Pink"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color2" name="color" value="peach" class="color-input">
            <label for="color2" class="color-label" style="background-color: #f5edd6;" title="Peach"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color3" name="color" value="mint" class="color-input">
            <label for="color3" class="color-label" style="background-color: #e9f5e6;" title="Mint"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color4" name="color" value="light-blue" class="color-input">
            <label for="color4" class="color-label" style="background-color: #d6f0f5;" title="Light blue"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color5" name="color" value="lavender" class="color-input">
            <label for="color5" class="color-label" style="background-color: #f0e6f5;" title="Lavender"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color6" name="color" value="lemon" class="color-input">
            <label for="color6" class="color-label" style="background-color: #f5f0d6;" title="Lemon"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color7" name="color" value="aqua" class="color-input">
            <label for="color7" class="color-label" style="background-color: #e6f2f5;" title="Aqua"></label>
        </div>
        <div class="color-option">
            <input type="radio" id="color8" name="color" value="beige" class="color-input">
            <label for="color8" class="color-label" style="background-color: #f5e6d6;" title="Beige"></label>
        </div>
    </div>
    <label for="notification" class="form-label">Send Notifications?</label>
    <input type="checkbox" name="notifications" class="cm-toggle">
    </div>
    <button type="submit" class="btn btn-primary" id="addSave" name="addSave" value="addSave">Save</button>
    <button type="submit" class="btn btn-primary" id="addSaveTrigger" name="addSaveTrigger" value="addSaveTrigger">Save & Trigger</button>

</form>

{include file="footer.tpl"}