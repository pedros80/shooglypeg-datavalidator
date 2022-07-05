<?php

namespace ShooglyPeg\DataValidator\Tests;

use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use ShooglyPeg\DataValidator\DataValidator;
use ShooglyPeg\DataValidator\Config;
use ShooglyPeg\DataValidator\Exceptions\JsonDataInvalid;
use ShooglyPeg\DataValidator\Exceptions\JsonSchemaNotFound;
use ShooglyPeg\DataValidator\Exceptions\JsonSchemaValidationFailed;

final class DataValidatorTest extends TestCase
{
    /**
     * @var DataValidator
     */
    private DataValidator $dataValidator;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->dataValidator = new DataValidator(new Config([
            'schemas' => 'schemas',
            'prefix'  => 'http://www.shooglypeg.co.uk/',
            'adapter' => new LocalFilesystemAdapter('./schemas')
        ]));
    }

    /**
     * @return void
     */
    public function testValidJsonAccordingToSchemaPasses(): void
    {
        $data = '{
            "data": {
                "timelines":[{
                    "timestep": "5m",
                    "startTime": "2020-05-10T06:49:34+0000",
                    "endTime":  "2020-05-10T08:49:34+0000",
                    "intervals": [{
                            "startTime": "2020-05-10T06:49:34+0000",
                            "values": {
                                "weatherCode": 2,
                                "temperature": 300
                            }
                        }
                    ]
                }]
            }
        }';

        $this->assertTrue($this->dataValidator->validate($data, 'weather'));
    }

    /**
     * @return void
     */
    public function testThrowsWhenDataIsNotValidAccordingToSchema(): void
    {
        $this->expectException(JsonSchemaValidationFailed::class);
        $this->expectExceptionMessage(
            'Json Schema Validation Failed: Additional object properties are not allowed: test'
        );

        $data = '{
            "data": {
                "timelines":[{
                    "timestep": "5m",
                    "startTime": "2020-05-10T06:49:34+0000",
                    "endTime":  "2020-05-10T08:49:34+0000",
                    "intervals": [{
                            "startTime": "2020-05-10T06:49:34+0000",
                            "values": {
                                "weatherCode": 2,
                                "temperature": 300,
                                "test": "test"
                            }
                        }
                    ]
                }]
            }
        }';

        $this->dataValidator->validate($data, 'weather');
    }

    /**
     * @return void
     */
    public function testThrowsWhenJsonSchemaIsNotFound(): void
    {
        $this->expectException(JsonSchemaNotFound::class);
        $this->expectExceptionMessage("Json Schema 'not_a_valid_json_schema.json' not found.");

        $data = '{
            "data": {
                "timelines":[{
                    "timestep": "5m",
                    "startTime": "2020-05-10T06:49:34+0000",
                    "endTime":  "2020-05-10T08:49:34+0000",
                    "intervals": [{
                            "startTime": "2020-05-10T06:49:34+0000",
                            "values": {
                                "weatherCode": 2,
                                "temperature": 300
                            }
                        }
                    ]
                }]
            }
        }';

        $this->dataValidator->validate($data, 'not_a_valid_json_schema');
    }

    /**
     * @return void
     */
    public function testThrowsWhenDataIsNotValidJson(): void
    {
        $this->expectException(JsonDataInvalid::class);
        $this->expectExceptionMessage('Json Data Invalid: Syntax error');

        $data = '{
            "data": {
                "timelines":[{
                    "timestep": "5m",
                    "startTime": "2020-05-10T06:49:34+0000",
                    "endTime":  "2020-05-10T08:49:34+0000",
                    "intervals": [{
                            "startTime": "2020-05-10T06:49:34+0000",
                            "values": {
                                "weatherCode": 2,
                                "temperature": 300
                            },
                        },
                    ],
                }],
            },
        }';

        $this->dataValidator->validate($data, 'weather');
    }
}
