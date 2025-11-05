SleeperApiSymfonyBundle Documentation
=====================================

Available documentation for SleeperApiSymfonyBundle
---------------------------------------------------

* [Introduction](introduction.md)
* [Setup](setup.md)
* [Entities](entities.md)
* [Field mapping](field_mapping.md)
* [Import strategies](import_strategies.md)
* [Extending the bundle](extending_the_bundle.md)
* [Contribute](contribute.md)
* [Roadmap](roadmap.md)

Important required index for matchup batch importing
----------------------------------------------------

To allow matchup bulk imports the sasb_sleeper_matchup table has to use an automatically created primary key. You can easily add it by adding the following snippet to your migrations:

.. code-block:: php
:linenos:

   <?php
    final class VersionXXX extends AbstractMigration
    {
        public function up(Schema $schema): void
        {
            $this->addSql(<<<'SQL'
                CREATE SEQUENCE IF NOT EXISTS sasb_sleeper_matchup_id_seq OWNED BY public.sasb_sleeper_matchup.id;
            SQL);
            $this->addSql(<<<'SQL'
                ALTER TABLE public.sasb_sleeper_matchup
                    ALTER COLUMN id SET DEFAULT nextval('sasb_sleeper_matchup_id_seq');
            SQL);
        }
    }
