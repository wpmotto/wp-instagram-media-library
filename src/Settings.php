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
            __( 'Instagram Media Library', 'motto-igml' ), 
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
                <input type='text' name='igml_settings[username]' value='<?php echo $options['username'] ?? null; ?>'>
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
                <input type='checkbox' name='igml_settings[sync_off]' <?php checked( $options['sync_off'] ?? null, 1 ); ?> value='1'>
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
                <select name='igml_settings[frequency]'>
                    <?php foreach($frequencies as $freq): ?>
                    <option value='<?php echo $freq ?>' <?php selected( $options['frequency'] ?? 0, $freq ); ?>><?php echo ucfirst($freq) ?></option>
                    <?php endforeach; ?>
                </select>
            
            <?php
            }, 
            'media', 
            'igml_media_section' 
        );        
    }    
}
