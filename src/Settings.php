<?php

namespace Motto\InstagramMediaLibrary;

class Settings {

    private $options;

    public function __construct()
    {
        $this->options = get_option('igml_settings') ?? [];
    }

    public function __get( $name )
    {
        if( isset($this->options[$name]) )
            return $this->options[$name];
    }

    public function media() 
    {
        register_setting( 'media', 'igml_settings' );
    
        add_settings_section(
            'igml_media_section', 
            __( 'Social Media Library', 'motto-igml' ), 
            function() { 
                // echo __( 'Instagram Media Library', 'motto-igml' );
            }, 
            'media'
        );
    
        add_settings_field( 
            'igml_account_username', 
            __( 'Instagram Account Username', 'motto-igml' ), 
            function() { 
                $options = get_option( 'igml_settings' );
                ?>
                <input type="<?php echo esc_attr('text') ?>" name="<?php echo esc_attr('igml_settings[username]') ?>" value='<?php echo esc_attr($options['username']) ?? null; ?>'>
                <?php
            }, 
            'media', 
            'igml_media_section' 
        );

        add_settings_field( 
            'igml_radidapi_key', 
            __( 'RapidAPI Key', 'motto-igml' ), 
            function() { 
                $options = get_option( 'igml_settings' );
                ?>
                <input type="<?php echo esc_attr('text') ?>" name="<?php echo esc_attr('igml_settings[rapidapi_key]') ?>" value="<?php echo esc_attr($options['rapidapi_key']) ?? null; ?>">
                <p><?php echo sprintf(__('Enter your <a href="%s" target="_blank">RapidAPI Key</a> in order use a proxy to avoid your IP from being blocked.', 'motto-igml'), 'https://rapidapi.com/restyler/api/instagram40') ?></p>

                <?php
            }, 
            'media', 
            'igml_media_section' 
        );
    
        add_settings_field( 
            'igml_sync_off', 
            __( 'Turn Off Instagram Sync', 'motto-igml' ), 
            function() { 
                $options = get_option( 'igml_settings' );
                ?>
                <input type="<?php echo esc_attr('checkbox') ?>" name="<?php echo esc_attr('igml_settings[sync_off]') ?>" <?php checked( $options['sync_off'] ?? null, 1 ); ?> value="<?php echo esc_attr('1') ?>">
                <?php
            }, 
            'media', 
            'igml_media_section' 
        );

        add_settings_field( 
            'igml_run_now', 
            __( 'Run Now', 'motto-igml' ), 
            function() { 
                ?>
                <input type="<?php echo esc_attr('checkbox') ?>" name="<?php echo esc_attr('igml_settings[run_now]') ?>" value="<?php echo esc_attr('1') ?>">
                <?php
            }, 
            'media', 
            'igml_media_section' 
        );

        add_settings_field( 
            'igml_frequency', 
            __( 'Sync Frequency', 'motto-igml' ), 
            function() { 

                $options = get_option( 'igml_settings' );
                $frequencies = [
                    'daily',
                    'hourly',
                    'twicedaily',
                    'weekly',
                ];
                ?>
                <select name="<?php echo esc_attr('igml_settings[frequency]') ?>">
                    <?php foreach($frequencies as $freq): ?>
                    <option value="<?php echo esc_attr($freq) ?>" <?php selected( $options['frequency'] ?? 0, $freq ); ?>>
                        <?php echo esc_html(ucfirst($freq)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            
            <?php
            }, 
            'media', 
            'igml_media_section' 
        );        
    }
    
    public function canSyncInstagram()
    {
        return !is_null($this->username) && !$this->sync_off && !is_null($this->rapidapi_key);
    }
}
