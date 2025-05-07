<section class="container contact-us">
    <h1>Get In Touch</h1>
    <?php
    echo flashdata();
    echo form_open($form_location);
    ?>

    <div class="form-group">
        <?= validation_errors('name'); ?>
        <?php echo form_input('name', $name, ['id' => 'name', 'required' => 'required', 'autocomplete' => 'on']); ?>
        <label for="name">Your Name</label>
    </div>

    <div class="form-group">
        <?= validation_errors('email_address'); ?>
        <?php echo form_email('email_address', $email_address, ['id' => 'email', 'required' => 'required', 'autocomplete' => 'on']); ?>
        <label for="email">Your Email Address</label>
    </div>

    <div class="form-group">
        <?= validation_errors('message'); ?>
        <?php echo form_textarea('message', $message, ['id' => 'message', 'rows' => 5, 'required' => 'required']); ?>
        <label for="message">Your Message</label>
    </div>

    <p class="prove">Prove you're human by answering the question below!</p>

    <div class="form-group">
        <?= validation_errors('answer'); ?>
        <?php echo form_dropdown('answer', $options, $answer, ['id' => 'answer', 'required' => 'required']); ?>
        <label for="answer"><?php echo $question; ?></label>
    </div>

    <?php
    echo form_submit('submit', 'Submit', ['class' => 'btn submit-btn']);
    echo anchor(BASE_URL, 'Cancel', array('class' => 'btn alt'));
    echo form_close();
    ?>
</section>
<style>
.contact-us {
    max-width: 600px;
    margin: 0 auto;
    padding-bottom: 120px;
}

.contact-us h1 {
    margin-bottom: 3rem;
}

.form-group {
    position: relative;
    margin-bottom: 2rem;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 1rem 0.75rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 0.5rem;
    background: none;
    outline: none;
}

.form-group label {
    position: absolute;
    top: 1rem;
    left: 0.75rem;
    color: #999;
    background: white;
    padding: 0 0.25rem;
    font-size: 0.9rem;
    transition: 0.2s ease all;
    pointer-events: none;
}

/* When input is focused OR has content, move label up */
.form-group input:focus + label,
.form-group input:not(:placeholder-shown) + label,
.form-group textarea:focus + label,
.form-group textarea:not(:placeholder-shown) + label,
.form-group select:focus + label,
.form-group select:not([value=""]) + label {
    top: -0.6rem;
    left: 0.6rem;
    font-size: 0.75rem;
    color: #333;
}

textarea {
    resize: vertical;
}

.prove { color: var(--prime-40); font-style: italic; text-align: center; }

.submit-btn {
    padding: 0.75rem 1.5rem;
    background-color: var(--prime-70) !important;
    color: white !important;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    cursor: pointer;
    margin-right: 1rem;
}

.submit-btn:hover {
    background-color: var(--primary-color) !important;
    color: white;
}

.btn.alt:hover {
    color: white;
    background-color: var(--highlight-color);
}

</style>