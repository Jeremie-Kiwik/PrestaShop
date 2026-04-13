<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;

/**
 * Contract for handling AddExtraPropertyDefinitionCommand.
 *
 * Implementations register a new core extra property definition and ensure the
 * corresponding SQL column exists in the entity's extra table.
 *
 * Returns the new definition's primary key on success.
 */
interface AddExtraPropertyDefinitionCommandHandlerInterface
{
    /**
     * @param AddExtraPropertyDefinitionCommand $command
     *
     * @return int The new definition's primary key
     *
     * @throws ExtraPropertyDomainException
     */
    public function handle(AddExtraPropertyDefinitionCommand $command): int;
}
