<div style="margin:0px auto;max-width:800px;font-size:18px;line-height:1.5;">
    <p class="">Dear Customer (<?php echo $email;?>),<br>
    </p>
    <p class="">We are excited to
        introduce our newly developed <strong>Task Manager</strong>, designed to enhance
        collaboration and improve efficiency in managing your requests.</p>
    <p class="">With this new tool, you
        can now:<br>
    </p>
    <ul>
        <li><strong>Add Notes</strong> -
            Provide additional details and context for your tasks.</li>
        <li><strong>Validate Changes</strong>
            - Review and approve modifications before we push them to
            production.</li>
        <li><strong>Upload Pictures</strong>
            - Attach images to clarify requests and feedback.<strong>
                (coming soon)<br>
            </strong></li>
    </ul>
    <p>This ensures a smoother
        workflow and helps us deliver exactly what you need with greater
        accuracy.</p>
    <p>We invite you to start
        using the Task Manager today and experience the benefits
        firsthand. If you have any questions or need assistance, feel free
        to reach out.</p>
    <table align='center' cellpadding="10" cellspacing="2" border="1" width="700">
        <tbody>
            <tr>
                <td valign="top"><b>Task Manager</b></td>
            </tr>
            <tr>
                <td valign="top"><a style='text-decoration: none; color:#000000;' href="https://app.tweezzo.online/portal/customers/signin"
                        class="moz-txt-link-freetext">https://app.tweezzo.online/portal/customers/signin</a></td>
            </tr>
            <tr>
                <td>You will receive another email for your account details, including your login credentials</td>
            </tr>
            <tr>
                <td>The portal easily allow your initiate the Forgot Password process, whenever you may need to reset your password (lost, forgot or for security reason). </td>
            </tr>
            <!-- <tr>
                <td valign="top">Email: <a class="" href="mailto:homeclassicsltd@gmail.com"><?php //echo $email;?></a>
                </td>
            </tr>
            <tr>
                <td valign="top">Password: <?php //echo $password;?></td>
            </tr> -->
        </tbody>
    </table>
    <br>
    <p class="">The application is very
        simple to use. After successful sign in, you will see a list of
        tasks for your projects. <br>
        Click on View to see details about a task. Once you are viewing a
        task, you can</p>
    <ul>
        <li class="">Add notes/remarks to
            it. Developers will be a able to view these and can respond or
            take actions accordingly</li>
        <li class="">In a task lifecycle,
            as shown in our diagram below, it starts as <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#1c8be6'>new</span>, then, once
            our team starts working on it, it will change to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#44ab8e'>in progress</span>.
            After completion, it will be changed to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#98c363'>testing</span> while it
            is being tested locally. If all is good, it will be changed to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#f36930'>staging</span>.</li>
        <li class="">This is where you are
            required to verify if the task meets the requirements.&nbsp;</li>
        <li class="">If it does, please
            click on the <b>Validate</b> button (when viewing a task). This
            will change the task stage to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#c44866'>validated</span>.</li>
        <li class="">Tasks are found in a
            <b>sprint</b> (which is a collection of tasks over a certain
            period of time). When all tasks in a sprint have been validated,
            they will be pushed to the <b>production server</b> and tasks stage will then be changed to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#4e67c7'>completed</span>.<br>
        </li>
        <li class="">However, if it does
            not meet the requirements, you should not click on the <b>Validate</b>
            button but rather enter some <b>notes</b> explaining what is
            incorrect. The task will be changed to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#44ab8e'>in progress</span> to
            proceed with the corrections. However, if our team need additional information to 
            be able proceed with the corrections, the stage will be set to <span style='padding:3px 6px; border-radius:3px; text-align:center; text-transform:uppercase; color: #fff; background-color:#ff0000'>on hold</span>.<br>
        </li>
    </ul>
</div>

<div style="margin:0px auto;max-width:800px;font-size:18px;line-height:1.5;">
    <img style='max-width:100%; padding:10px; border:1px solid #ccc;' src="<?php echo base_url("assets/images/task-lifecycle-2.png");?>" alt="">
</div>