<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216085813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id UUID NOT NULL, city_id UUID NOT NULL, address VARCHAR(500) NOT NULL, postal_code VARCHAR(50) DEFAULT NULL, street VARCHAR(100) NOT NULL, house VARCHAR(50) NOT NULL, flat VARCHAR(50) DEFAULT NULL, entrance VARCHAR(50) DEFAULT NULL, floor VARCHAR(50) DEFAULT NULL, is_active BOOLEAN DEFAULT true NOT NULL, point POINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81D4E6F81 ON address (address)');
        $this->addSql('CREATE INDEX IDX_D4E6F818BAC62AF ON address (city_id)');
        $this->addSql('CREATE INDEX idx_address_created_at ON address (created_at) WHERE (is_active = false)');
        $this->addSql('COMMENT ON TABLE address IS \'Адреса\'');
        $this->addSql('COMMENT ON COLUMN address.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN address.city_id IS \'Город(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN address.address IS \'Строка адреса\'');
        $this->addSql('COMMENT ON COLUMN address.postal_code IS \'Почтовый индекс\'');
        $this->addSql('COMMENT ON COLUMN address.street IS \'Улица\'');
        $this->addSql('COMMENT ON COLUMN address.house IS \'Дом\'');
        $this->addSql('COMMENT ON COLUMN address.flat IS \'Квартира\'');
        $this->addSql('COMMENT ON COLUMN address.entrance IS \'Подъезд\'');
        $this->addSql('COMMENT ON COLUMN address.floor IS \'Этаж\'');
        $this->addSql('COMMENT ON COLUMN address.is_active IS \'Активный ли адрес\'');
        $this->addSql('COMMENT ON COLUMN address.point IS \'Широта и Долгота\'');
        $this->addSql('COMMENT ON COLUMN address.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN address.updated_at IS \'Дата обновления\'');
        $this->addSql('CREATE TABLE city (id UUID NOT NULL, region_id UUID NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2D5B023498260155 ON city (region_id)');
        $this->addSql('CREATE INDEX idx_city_created_at ON city (created_at) WHERE (is_active = false)');
        $this->addSql('CREATE UNIQUE INDEX city_unique_region_id_type_name ON city (region_id, type, name)');
        $this->addSql('COMMENT ON TABLE city IS \'Города\'');
        $this->addSql('COMMENT ON COLUMN city.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN city.region_id IS \'Регион(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN city.name IS \'Название города\'');
        $this->addSql('COMMENT ON COLUMN city.type IS \'Тип города\'');
        $this->addSql('COMMENT ON COLUMN city.is_active IS \'Активный ли город\'');
        $this->addSql('COMMENT ON COLUMN city.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN city.updated_at IS \'Дата обновления\'');
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, name VARCHAR(250) NOT NULL, numeric_code SMALLINT NOT NULL, alpha2 VARCHAR(2) NOT NULL, alpha3 VARCHAR(3) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C9665E237E06 ON country (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C96695079952 ON country (numeric_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C966B762D672 ON country (alpha2)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C966C065E6E4 ON country (alpha3)');
        $this->addSql('CREATE INDEX idx_country_created_at ON country (created_at) WHERE (is_active = false)');
        $this->addSql('COMMENT ON TABLE country IS \'Страны\'');
        $this->addSql('COMMENT ON COLUMN country.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN country.name IS \'Название страны\'');
        $this->addSql('COMMENT ON COLUMN country.numeric_code IS \'Код страны\'');
        $this->addSql('COMMENT ON COLUMN country.alpha2 IS \'Код Alpha2 страны\'');
        $this->addSql('COMMENT ON COLUMN country.alpha3 IS \'Код Alpha3 страны\'');
        $this->addSql('COMMENT ON COLUMN country.is_active IS \'Активная ли страна\'');
        $this->addSql('COMMENT ON COLUMN country.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN country.updated_at IS \'Дата обновления\'');
        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, code VARCHAR(3) NOT NULL, num INT NOT NULL, name VARCHAR(100) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883F77153098 ON currency (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883FDC43AF6E ON currency (num)');
        $this->addSql('CREATE INDEX idx_currency_name ON currency (name) WHERE (is_active = true)');
        $this->addSql('CREATE INDEX idx_currency_created_at ON currency (created_at) WHERE (is_active = true)');
        $this->addSql('COMMENT ON TABLE currency IS \'Валюта\'');
        $this->addSql('COMMENT ON COLUMN currency.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN currency.code IS \'Уникальный код валюты\'');
        $this->addSql('COMMENT ON COLUMN currency.num IS \'Уникальный номер валюты\'');
        $this->addSql('COMMENT ON COLUMN currency.name IS \'Название\'');
        $this->addSql('COMMENT ON COLUMN currency.is_active IS \'Активная ли валюта\'');
        $this->addSql('COMMENT ON COLUMN currency.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN currency.updated_at IS \'Дата обновления\'');
        $this->addSql('CREATE TABLE currency_rate (id UUID NOT NULL, rate DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, currencyFrom UUID NOT NULL, currencyTo UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_555B7C4D86C98FAF ON currency_rate (currencyFrom)');
        $this->addSql('CREATE INDEX IDX_555B7C4DF9BEBE1E ON currency_rate (currencyTo)');
        $this->addSql('CREATE INDEX idx_currency_rate_from_to ON currency_rate (currencyFrom, currencyTo) WHERE (expired_at is null)');
        $this->addSql('CREATE INDEX idx_currency_rate_created_at ON currency_rate (created_at) WHERE (expired_at is null)');
        $this->addSql('COMMENT ON TABLE currency_rate IS \'Конвертация валюты\'');
        $this->addSql('COMMENT ON COLUMN currency_rate.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN currency_rate.rate IS \'Ставка\'');
        $this->addSql('COMMENT ON COLUMN currency_rate.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN currency_rate.expired_at IS \'Дата окончания действия записи\'');
        $this->addSql('COMMENT ON COLUMN currency_rate.currencyFrom IS \'Из валюты(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN currency_rate.currencyTo IS \'В валюту(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE language (id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, logo TEXT DEFAULT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4DB71B577153098 ON language (code)');
        $this->addSql('COMMENT ON TABLE language IS \'Языки\'');
        $this->addSql('COMMENT ON COLUMN language.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN language.name IS \'Название\'');
        $this->addSql('COMMENT ON COLUMN language.code IS \'Код\'');
        $this->addSql('COMMENT ON COLUMN language.logo IS \'Логотип\'');
        $this->addSql('COMMENT ON COLUMN language.is_active IS \'Активный ли язык\'');
        $this->addSql('COMMENT ON COLUMN language.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN language.updated_at IS \'Дата обновления\'');
        $this->addSql('CREATE TABLE region (id UUID NOT NULL, country_id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(7) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F1765E237E06 ON region (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F17677153098 ON region (code)');
        $this->addSql('CREATE INDEX IDX_F62F176F92F3E70 ON region (country_id)');
        $this->addSql('CREATE INDEX idx_region_created_at ON region (created_at) WHERE (is_active = false)');
        $this->addSql('COMMENT ON TABLE region IS \'Регионы\'');
        $this->addSql('COMMENT ON COLUMN region.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN region.country_id IS \'Страна(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN region.name IS \'Название региона\'');
        $this->addSql('COMMENT ON COLUMN region.code IS \'Код региона\'');
        $this->addSql('COMMENT ON COLUMN region.is_active IS \'Активный ли регион\'');
        $this->addSql('COMMENT ON COLUMN region.created_at IS \'Дата создания\'');
        $this->addSql('COMMENT ON COLUMN region.updated_at IS \'Дата обновления\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023498260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE currency_rate ADD CONSTRAINT FK_555B7C4D86C98FAF FOREIGN KEY (currencyFrom) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE currency_rate ADD CONSTRAINT FK_555B7C4DF9BEBE1E FOREIGN KEY (currencyTo) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP CONSTRAINT FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE city DROP CONSTRAINT FK_2D5B023498260155');
        $this->addSql('ALTER TABLE currency_rate DROP CONSTRAINT FK_555B7C4D86C98FAF');
        $this->addSql('ALTER TABLE currency_rate DROP CONSTRAINT FK_555B7C4DF9BEBE1E');
        $this->addSql('ALTER TABLE region DROP CONSTRAINT FK_F62F176F92F3E70');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE currency_rate');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
