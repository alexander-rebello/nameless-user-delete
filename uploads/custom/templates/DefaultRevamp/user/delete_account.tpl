{include file='header.tpl'}
{include file='navbar.tpl'}

<h2 class="ui header">
    {$DELETE_ACCOUNT}
</h2>

{if !empty($SUCCESS_MESSAGE)}
<div class="ui success icon message">
    <i class="check icon"></i>
    <div class="content">
        <div class="header">Success</div>
        {$SUCCESS_MESSAGE}
    </div>
</div>
{/if}

{if (isset($ERRORS) || isset($ERROR))}
<div class="ui error icon message">
    <i class="x icon"></i>
    <div class="content">
        <ul class="list">
            {foreach from=$ERRORS item=error}
            <li>{$error}</li>
            {/foreach}
            {if isset($ERROR)}
            <li>{$ERROR}</li>
            {/if}
        </ul>
    </div>
</div>
{/if}

<div class="ui stackable grid" id="user-settings">
    <div class="ui centered row">
        <div class="ui six wide tablet four wide computer column">
            {include file='user/navigation.tpl'}
        </div>
        <div class="ui ten wide tablet twelve wide computer column">
            <div class="ui segment">
                <h3 class="ui header">{$DELETE_ACCOUNT}</h3>
                <div class="ui warning message">
                    <i class="warning icon"></i>
                    <div class="content">
                        <div class="header">{$WARNING}</div>
                        {$DELETE_ACCOUNT_INFO}
                    </div>
                </div>
                <form class="ui form" action="" method="post" id="form-user-settings">
                    <div class="field">
                        <label for="password">
                            {$CURRENT_PASSWORD}
                            <span style="color: red;">*</span>
                        </label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="field">
                        <label for="username_confirm">
                            {$USERNAME_CONFIRM}
                            <span style="color: red;">*</span>
                        </label>
                        <input type="text" name="username_confirm" id="username_confirm" placeholder="{$USERNAME}" required>
                        <small>{$USERNAME_CONFIRM_INFO}</small>
                    </div>
                    <input type="hidden" name="token" value="{$TOKEN}">
                    <div class="ui buttons">
                        <button type="submit" class="ui red button">{$DELETE_ACCOUNT_BUTTON}</button>
                        <div class="or"></div>
                        <a href="/user" class="ui button">{$CANCEL}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{include file='footer.tpl'}
