<?php
/* nothing to do: codice fisscale is a 16 char alphanumerical code and no more */
class profile_define_codicefiscale extends profile_define_base {
/**
     * Add elements for creating/editing a text profile field.
     * @param moodleform $form
     */
    public function define_form_specific($form) {
        // Default data.
        $form->addElement('text', 'defaultdata', get_string('profiledefaultdata', 'admin'), 'size="50"');
        $form->setType('defaultdata', PARAM_TEXT);

     }
}
