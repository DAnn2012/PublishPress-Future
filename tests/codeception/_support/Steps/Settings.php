<?php

namespace Steps;

trait Settings
{
    /**
     * @Given expiration metabox is enabled for :postType
     */
     public function expirationMetaboxIsEnabledForPostType($postType)
     {
        $this->haveOptionInDatabase(
            'expirationdateDefaults' . strtoupper($postType),
            [
                'expireType' => 'draft',
                'autoEnable' => '0',
                'activeMetaBox' => 'active',
                'emailnotification' => '',
                'default-expire-type' => '',
                'default-custom-date' => '',
            ]
        );
     }

     /**
     * @Given expiration metabox is disabled for :postType
     */
    public function expirationMetaboxIsDisabledForPostType($postType)
    {
       $this->haveOptionInDatabase(
           'expirationdateDefaults' . strtoupper($postType),
           [
               'expireType' => 'draft',
               'autoEnable' => '0',
               'activeMetaBox' => 'inactive',
               'emailnotification' => '',
               'default-expire-type' => '',
               'default-custom-date' => '',
           ]
       );
    }

    /**
     * @Given default expiration is not activated for :postType
     */
    public function defaultExpirationNotActivatedForPostType($postType)
    {
        $this->haveOptionInDatabase(
            'expirationdateDefaults' . strtoupper($postType),
            [
                'expireType' => 'draft',
                'autoEnable' => '0',
                'activeMetaBox' => 'active',
                'emailnotification' => '',
                'default-expire-type' => '',
                'default-custom-date' => '',
            ]
        );
    }

    /**
     * @Given default expiration is activated for :postType
     */
    public function defaultExpirationActivatedForPostType($postType)
    {
        $this->haveOptionInDatabase(
            'expirationdateDefaults' . strtoupper($postType),
            [
                'expireType' => 'draft',
                'autoEnable' => '1',
                'activeMetaBox' => 'active',
                'emailnotification' => '',
                'default-expire-type' => '',
                'default-custom-date' => '',
            ]
        );
    }

    /**
     * @Given I am on the settings page in the Post Types tab
     */
    public function iAmOnTheSettingsPageInThePostTypesTab()
    {
        $this->amOnAdminPage('admin.php?page=publishpress-future&tab=defaults');
    }

   /**
    * @When /I change the default taxonomy to ([a-z_0-9]+) for ([a-z_0-9]+)/
    */
    public function iChangeTheDefaultTaxonomyToFor($taxonomy, $postType)
    {
        $this->selectOption('#expirationdate_taxonomy-' . $postType, $taxonomy);
    }

   /**
    * @When I save the changes
    */
    public function iSaveTheChanges()
    {
        $this->click('Save Changes');
    }

   /**
    * @Then /I see the taxonomy ([a-z_0-9]+) as the default one for ([a-z_0-9]+)/
    */
    public function iSeeTheTaxonomyAsTheDefaultOneFor($taxonomy, $postType)
    {
        $this->seeOptionIsSelected('#expirationdate_taxonomy-' . $postType, $taxonomy);
    }

    /**
     * @When /I set Active field as (inactive|active) for ([a-z_0-9]+)/
     */
    public function iSetActiveFieldFor($value, $postType)
    {
        $this->selectOption('input[name="expirationdate_activemeta-'  . $postType . '"]', $value);
    }

   /**
    * @When /I see the field Active has value (inactive|active) for ([a-z_0-9]+)/
    */
    public function iSeeTheFieldActiveHasValueFor($value, $postType)
    {
        $this->seeOptionIsSelected('input[name="expirationdate_activemeta-'  . $postType . '"]', ucfirst($value));
    }

    /**
     * @When I enable auto-enable for :post
     */
    public function iEnableAutoenableForPost($postType)
    {
        $this->click("#expirationdate_autoenable-true-{$postType}");
    }

    /**
     * @When I disable auto-enable for :post
     */
    public function iDisableAutoenableForPost($postType)
    {
        $this->click("#expirationdate_autoenable-false-{$postType}");
        // $this->wait(30);
    }

   /**
    * @Then I see auto-enable is selected for :postType
    */
    public function iSeeAutoenableIsSelectedForPost($postType)
    {
        $this->seeOptionIsSelected("input[name=expirationdate_autoenable-{$postType}]", 'Enabled');
    }

    /**
    * @Then I see auto-enable is not selected for :postType
    */
    public function iSeeAutoenableIsNotSelectedForPost($postType)
    {
        $this->seeOptionIsSelected("input[name=expirationdate_autoenable-{$postType}]", 'Disabled');
    }

    /**
     * @When I set How To Expire as :value for :postType
     */
    public function iSetHowToExpireAsDeleteForPost($value, $postType)
    {
        $this->selectOption('#expirationdate_expiretype-' . $postType, $value);
    }

    /**
     * @Then I see the field How to Expire has value :value for :postType
     */
    public function iSeeTheFieldHowToExpireHasValueFor($value, $postType)
    {
        $this->seeOptionIsSelected('#expirationdate_expiretype-' . $postType, $value);
    }

    /**
     * @When I set Auto-Enable as :value for :postType
     */
    public function iSetAutoEnableAsFor($value, $postType)
    {
        if ($value === 'Enable') {
            $this->click("#expirationdate_autoenable-true-{$postType}");
        } else {
            $this->click("#expirationdate_autoenable-false-{$postType}");
        }
    }

   /**
    * @Then I see the field Auto-Enable has value :value for :postType
    */
    public function iSeeTheFieldAutoEnableHasValueFor($value, $postType)
    {
        $this->seeOptionIsSelected("input[name=expirationdate_autoenable-{$postType}]", $value);
    }

    /**
     * @When I set Taxonomy as :value for :postType
     */
    public function iSetTaxonomyAsFor($value, $postType)
    {
        $this->selectOption('#expirationdate_taxonomy-' . $postType, $value);
    }

   /**
    * @Then I see the field Taxonomy has value :value for :postType
    */
    public function iSeeTheFieldTaxonomyHasValueFor($value, $postType)
    {
        $this->seeOptionIsSelected("#expirationdate_taxonomy-" . $postType, $value);
    }
}
