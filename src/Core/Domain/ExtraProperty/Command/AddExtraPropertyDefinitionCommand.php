<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

/**
 * Command to register a new core extra property definition.
 *
 * Only '_core' fields (empty module name) may be created through the Back-Office UI.
 * The module_name is intentionally absent: it is always set to '' (empty string) for
 * fields managed via this command.
 */
class AddExtraPropertyDefinitionCommand
{
    /**
     * @param string $entityName Entity table name (e.g. "product")
     * @param string $fieldName Logical field identifier (e.g. "custom_ref")
     * @param string $fieldType Type literal matching ExtraPropertyType (e.g. 'string', 'int')
     * @param string $fieldScope Scope literal matching ExtraPropertyScope ('common', 'lang', 'shop')
     * @param string $sqlIndex Index type ('none', 'key', 'unique')
     * @param string|null $titleWording i18n wording for the display label
     * @param string|null $titleDomain Translation domain for the display label
     * @param string|null $descriptionWording i18n wording for the field description
     * @param string|null $descriptionDomain Translation domain for the field description
     * @param bool $displayFront Whether the field is exposed to front-office templates
     * @param bool $displayApi Whether the field is exposed via the Admin API
     * @param bool $displayBo Whether the field appears in Back-Office entity forms
     * @param bool $displayGrid Whether the field appears as a column in Back-Office grids
     * @param int|null $gridPosition Column position in BO grids (null = auto / last)
     * @param string|null $validator Validate:: method name for value validation
     * @param string|null $symfonyFieldType Symfony form type FQCN override for the BO form widget
     */
    public function __construct(
        protected readonly string $entityName,
        protected readonly string $fieldName,
        protected readonly string $fieldType,
        protected readonly string $fieldScope = 'common',
        protected readonly string $sqlIndex = 'none',
        protected readonly ?string $titleWording = null,
        protected readonly ?string $titleDomain = null,
        protected readonly ?string $descriptionWording = null,
        protected readonly ?string $descriptionDomain = null,
        protected readonly bool $displayFront = false,
        protected readonly bool $displayApi = false,
        protected readonly bool $displayBo = true,
        protected readonly bool $displayGrid = false,
        protected readonly ?int $gridPosition = null,
        protected readonly ?string $validator = null,
        protected readonly ?string $symfonyFieldType = null,
    ) {
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    /**
     * @return string
     */
    public function getFieldScope(): string
    {
        return $this->fieldScope;
    }

    /**
     * @return string
     */
    public function getSqlIndex(): string
    {
        return $this->sqlIndex;
    }

    /**
     * @return string|null
     */
    public function getTitleWording(): ?string
    {
        return $this->titleWording;
    }

    /**
     * @return string|null
     */
    public function getTitleDomain(): ?string
    {
        return $this->titleDomain;
    }

    /**
     * @return string|null
     */
    public function getDescriptionWording(): ?string
    {
        return $this->descriptionWording;
    }

    /**
     * @return string|null
     */
    public function getDescriptionDomain(): ?string
    {
        return $this->descriptionDomain;
    }

    /**
     * @return bool
     */
    public function isDisplayFront(): bool
    {
        return $this->displayFront;
    }

    /**
     * @return bool
     */
    public function isDisplayApi(): bool
    {
        return $this->displayApi;
    }

    /**
     * @return bool
     */
    public function isDisplayBo(): bool
    {
        return $this->displayBo;
    }

    /**
     * @return bool
     */
    public function isDisplayGrid(): bool
    {
        return $this->displayGrid;
    }

    /**
     * @return int|null
     */
    public function getGridPosition(): ?int
    {
        return $this->gridPosition;
    }

    /**
     * @return string|null
     */
    public function getValidator(): ?string
    {
        return $this->validator;
    }

    /**
     * @return string|null
     */
    public function getSymfonyFieldType(): ?string
    {
        return $this->symfonyFieldType;
    }
}
